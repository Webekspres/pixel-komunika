<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentService
{
    /**
     * Store uploaded payment proof for an order.
     */
    public function uploadPaymentProof(Order $order, User $user, array $data, UploadedFile $file): PaymentProof
    {
        return DB::transaction(function () use ($order, $user, $data, $file) {
            $path = $file->store('payment-proofs', 'local');

            $proof = PaymentProof::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'bank_name' => $data['bank_name'],
                'account_name' => $data['account_name'],
                'amount' => $data['amount'],
                'proof_path' => $path,
                'status' => 'pending',
            ]);

            $order->update(['status' => 'payment_pending']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'payment_pending']);
            }

            return $proof;
        });
    }

    /**
     * Admin approves a payment proof.
     */
    public function approvePayment(PaymentProof $proof, User $admin): void
    {
        DB::transaction(function () use ($proof, $admin) {
            $proof->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $order = $proof->order;
            $order->update(['status' => 'paid']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'paid']);
            }
        });
    }

    /**
     * Admin rejects a payment proof.
     */
    public function rejectPayment(PaymentProof $proof, string $reason, User $admin): void
    {
        DB::transaction(function () use ($proof, $reason, $admin) {
            $proof->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            $order = $proof->order;
            $order->update(['status' => 'unpaid']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'unpaid']);
            }
        });
    }
}
