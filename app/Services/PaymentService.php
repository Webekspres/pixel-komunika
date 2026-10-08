<?php

namespace App\Services;

use App\Domains\Audit\AuditLogger;
use App\Domains\PosIntegration\PosOutboxService;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class PaymentService
{
    public function __construct(
        protected AuditLogger $audit,
        protected PosOutboxService $posOutbox,
    ) {}

    public function uploadPaymentProof(Order $order, User $user, array $data, UploadedFile $file): PaymentProof
    {
        return DB::transaction(function () use ($order, $user, $data, $file) {
            // UF-10: unggah hanya saat menunggu pembayaran / setelah ditolak.
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            // Keputusan 5 Okt: akun ditangguhkan tetap bisa melihat pesanan/invoice,
            // tetapi tidak bisa membayar sampai diaktifkan kembali.
            if (! $user->isActiveCustomer()) {
                throw ValidationException::withMessages([
                    'proof_file' => 'Akun Anda sedang tidak aktif. Hubungi admin untuk melanjutkan pembayaran.',
                ]);
            }

            if (! in_array($order->status, ['unpaid', 'payment_rejected'], true)) {
                throw ValidationException::withMessages([
                    'proof_file' => 'Bukti pembayaran tidak dapat diunggah untuk status pesanan saat ini.',
                ]);
            }

            $path = $file->store('payment-proofs', 'local');
            $retainYears = (int) config('store.payment_proof_retain_years', 5);

            $payment = $order->payment;
            if (! $payment) {
                $bank = BankAccount::defaultActive()
                    ?? BankAccount::query()->create([
                        'bank_name' => $data['bank_name'],
                        'account_number' => 'TEMP-'.$order->id,
                        'account_holder' => $data['account_name'],
                        'is_active' => true,
                    ]);

                $payment = Payment::create([
                    'order_id' => $order->id,
                    'bank_account_id' => $bank->id,
                    'status' => Payment::NOT_SUBMITTED,
                    'amount' => $data['amount'],
                ]);
            }

            $payment->proofs()->where('is_active', true)->update(['is_active' => false]);

            $proof = PaymentProof::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'amount' => $data['amount'],
                'proof_path' => $path,
                'checksum' => hash_file('sha256', $file->getRealPath()) ?: null,
                'retain_until' => now()->addYears($retainYears),
                'status' => 'pending',
                'is_active' => true,
            ]);

            $payment->update([
                'status' => Payment::SUBMITTED,
                'amount' => $data['amount'],
                'submitted_at' => now(),
                'rejection_reason' => null,
            ]);

            $order->update(['status' => 'payment_pending']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'payment_pending']);
            }

            $this->audit->log('PAYMENT_PROOF_UPLOADED', $proof, $user);

            return $proof;
        });
    }

    public function approvePayment(PaymentProof $proof, User $admin): void
    {
        DB::transaction(function () use ($proof, $admin) {
            [$proof, $order] = $this->lockReviewable($proof);

            $proof->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $proof->payment?->update([
                'status' => Payment::VERIFIED,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);

            $order->update(['status' => 'processing']);
            // CR-023: laporan penjualan POS dikirim H+1 setelah pesanan diproses (lihat SamplePosSyncService).
            $this->posOutbox->queueSaleReport($order);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'paid']);
            }

            $this->audit->log('PAYMENT_VERIFIED', $proof, $admin);
        });
    }

    public function rejectPayment(PaymentProof $proof, string $reason, User $admin): void
    {
        DB::transaction(function () use ($proof, $reason, $admin) {
            [$proof, $order] = $this->lockReviewable($proof);

            $proof->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'is_active' => false,
            ]);

            $proof->payment?->update([
                'status' => Payment::REJECTED,
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $order->update(['status' => 'payment_rejected']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'unpaid']);
            }

            $this->audit->log('PAYMENT_REJECTED', $proof, $admin, null, ['reason' => $reason]);
        });
    }

    /**
     * Kunci order + bukti dan pastikan masih bisa direview (FR-ORD-008): bukti aktif
     * berstatus pending pada order payment_pending. Mencegah approve/reject ganda
     * dan menghidupkan kembali order yang sudah dibatalkan.
     *
     * @return array{0: PaymentProof, 1: Order}
     */
    protected function lockReviewable(PaymentProof $proof): array
    {
        $order = Order::query()->lockForUpdate()->findOrFail($proof->order_id);
        $proof = PaymentProof::query()->lockForUpdate()->findOrFail($proof->id);

        if ($proof->status !== 'pending' || ! $proof->is_active || $order->status !== 'payment_pending') {
            throw new InvalidArgumentException("Bukti pembayaran Order #{$order->order_number} sudah diproses atau pesanan tidak lagi menunggu verifikasi.");
        }

        return [$proof, $order];
    }
}
