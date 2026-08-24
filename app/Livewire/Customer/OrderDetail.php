<?php

namespace App\Livewire\Customer;

use App\Models\BankAccount;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Detail Pesanan - Pixel Komunika')]
class OrderDetail extends Component
{
    use WithFileUploads;

    public Order $order;

    // Payment proof upload fields
    public $bank_name = '';

    public $account_name = '';

    public $amount = '';

    public $proof_file;

    // Return request fields
    public $return_reason = '';

    // UI state
    public bool $showCancelModal = false;

    public string $cancelReason = '';

    public ?string $proofPreviewUrl = null;

    public function mount(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $this->order = $order->load([
            'items.product.media.library',
            'invoice',
            'paymentProofs.reviewer',
            'returns',
            'shipment.storeCourierRate',
        ]);

        $this->amount = (string) (fmod((float) $order->grand_total, 1.0) == 0 ? (int) $order->grand_total : (float) $order->grand_total);
    }

    public function confirmCancelOrder(): void
    {
        $this->openCancelModal();
    }

    public function updatedProofFile(): void
    {
        if ($this->proof_file) {
            try {
                $ext = strtolower($this->proof_file->getClientOriginalExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                    $this->proofPreviewUrl = $this->proof_file->temporaryUrl();
                } else {
                    $this->proofPreviewUrl = null;
                }
            } catch (\Throwable) {
                $this->proofPreviewUrl = null;
            }
        } else {
            $this->proofPreviewUrl = null;
        }
    }

    public function uploadPaymentProof(PaymentService $paymentService): void
    {
        $this->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:1',
            'proof_file' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ]);

        $paymentService->uploadPaymentProof($this->order, Auth::user(), [
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'amount' => str_replace('.', '', $this->amount),
        ], $this->proof_file);

        session()->flash('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi admin.');
        $this->reset(['proof_file', 'bank_name', 'account_name', 'amount', 'proofPreviewUrl']);
        $this->order->refresh();
    }

    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    public function cancelOrder(OrderService $orderService): void
    {
        $this->validate([
            'cancelReason' => 'required|string|min:5|max:255',
        ]);

        if (! in_array($this->order->status, ['unpaid', 'payment_pending', 'payment_rejected'], true)) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan pada tahap ini.');
            $this->showCancelModal = false;

            return;
        }

        $orderService->cancelOrder($this->order, 'Dibatalkan oleh pelanggan: '.$this->cancelReason, 'CUSTOMER');

        session()->flash('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
        $this->showCancelModal = false;
        $this->reset('cancelReason');
        $this->order->refresh();
    }

    public function requestReturn(OrderService $orderService): void
    {
        $this->validate([
            'return_reason' => 'required|string|min:10',
        ]);

        $orderService->requestReturn($this->order, Auth::user(), $this->return_reason);

        session()->flash('success', 'Permohonan retur berhasil diajukan dan akan direview oleh admin.');
        $this->reset('return_reason');
        $this->order->refresh();
    }

    public function copyToClipboard(string $text): void
    {
        $this->dispatch('copy-to-clipboard', text: $text);
    }

    public function render(): View
    {
        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->oldest('id')
            ->get();

        $latestProof = $this->order->paymentProofs->last();

        return view('livewire.customer.order-detail', [
            'bankAccounts' => $bankAccounts,
            'latestProof' => $latestProof,
        ])
            ->layout('components.layouts.customer');
    }
}
