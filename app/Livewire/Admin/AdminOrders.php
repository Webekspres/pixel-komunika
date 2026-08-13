<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Admin - Manajemen Pesanan & Pembayaran')]
class AdminOrders extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'all';

    public string $search = '';

    public string $rejectionReason = '';

    public ?int $selectedProofId = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updateOrderStatus(int $orderId, string $newStatus)
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $newStatus]);
        if ($order->invoice && $newStatus === 'paid') {
            $order->invoice->update(['status' => 'paid']);
        }
        session()->flash('success', "Status pesanan {$order->order_number} diperbarui menjadi {$newStatus}.");
    }

    public function approvePayment(int $proofId, PaymentService $paymentService)
    {
        $proof = PaymentProof::findOrFail($proofId);
        $paymentService->approvePayment($proof, Auth::user());

        session()->flash('success', "Pembayaran untuk Order #{$proof->order->order_number} telah disetujui.");
    }

    public function rejectPayment(int $proofId, PaymentService $paymentService)
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:5',
        ]);

        $proof = PaymentProof::findOrFail($proofId);
        $paymentService->rejectPayment($proof, $this->rejectionReason, Auth::user());

        $this->reset(['rejectionReason', 'selectedProofId']);
        session()->flash('success', "Pembayaran untuk Order #{$proof->order->order_number} telah ditolak.");
    }

    public function cancelOrder(int $orderId, OrderService $orderService)
    {
        $order = Order::findOrFail($orderId);
        $orderService->cancelOrder($order, 'Dibatalkan oleh Admin', 'ADMIN');

        session()->flash('success', "Order #{$order->order_number} berhasil dibatalkan dan stok telah direstore.");
    }

    public function render()
    {
        $query = Order::with(['user', 'items', 'invoice', 'latestPaymentProof'])->latest();

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', "%{$this->search}%")
                    ->orWhere('recipient_name', 'like', "%{$this->search}%")
                    ->orWhere('recipient_phone', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.admin-orders', [
            'orders' => $query->paginate(15),
        ])->layout('components.layouts.app');
    }
}
