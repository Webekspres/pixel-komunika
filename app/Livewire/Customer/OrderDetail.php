<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
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

    public function mount(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $this->order = $order->load(['items', 'invoice', 'paymentProofs.reviewer', 'returns']);
        $this->amount = number_format($order->grand_total, 0, '', '');
    }

    public function uploadPaymentProof(PaymentService $paymentService)
    {
        $this->validate([
            'bank_name' => 'required|string|max:100',
            'account_name' => 'required|string|max:150',
            'amount' => 'required|numeric|min:1',
            'proof_file' => 'required|image|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ]);

        $paymentService->uploadPaymentProof($this->order, Auth::user(), [
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'amount' => $this->amount,
        ], $this->proof_file);

        session()->flash('success', 'Bukti pembayaran berhasil diunggah dan sedang menunggu verifikasi admin.');
        $this->reset(['proof_file']);
        $this->order->refresh();
    }

    public function cancelOrder(OrderService $orderService)
    {
        if (! in_array($this->order->status, ['unpaid', 'payment_pending'])) {
            session()->flash('error', 'Pesanan tidak dapat dibatalkan.');

            return;
        }

        $orderService->cancelOrder($this->order, 'Dibatalkan oleh pelanggan', 'CUSTOMER');

        session()->flash('success', 'Pesanan berhasil dibatalkan dan stok produk telah dikembalikan.');
        $this->order->refresh();
    }

    public function requestReturn(OrderService $orderService)
    {
        $this->validate([
            'return_reason' => 'required|string|min:10',
        ]);

        $orderService->requestReturn($this->order, Auth::user(), $this->return_reason);

        session()->flash('success', 'Permohonan retur berhasil diajukan dan akan direview oleh admin.');
        $this->reset(['return_reason']);
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.customer.order-detail')
            ->layout('components.layouts.customer');
    }
}
