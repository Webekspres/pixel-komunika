<?php

namespace App\Livewire\Admin;

use App\Domains\Notifications\NotificationService;
use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
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

    public function mount(NotificationService $notifications): void
    {
        // FR-NTF-001: seluruh notifikasi order baru dianggap dibaca saat admin
        // membuka daftar pesanan yang akan diproses.
        $user = Auth::user();
        if ($user) {
            $notifications->markOrderNotificationsRead($user);
            $this->dispatch('notifications-updated');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function transitionStatus(int $orderId, string $newStatus, FulfillmentService $fulfillment)
    {
        $order = Order::findOrFail($orderId);

        try {
            $fulfillment->transition($order, $newStatus, Auth::user());
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
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
        $orderService->cancelOrder($order, 'Dibatalkan oleh Admin', 'ADMIN', auth()->user());

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
