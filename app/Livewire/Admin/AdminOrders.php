<?php

namespace App\Livewire\Admin;

use App\Domains\Notifications\NotificationService;
use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use App\Services\OrderService;
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

    public bool $showCancelModal = false;

    public ?int $cancelOrderId = null;

    public string $cancelReason = '';

    public bool $showTerkendalaModal = false;

    public ?int $terkendalaOrderId = null;

    public string $terkendalaReason = '';

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

    // FR-ORD-006: pembatalan manual oleh admin hanya pada hari yang sama dengan
    // tanggal transaksi, untuk order yang belum dibayar atau menunggu verifikasi.
    public function canCancelToday(Order $order): bool
    {
        return in_array($order->status, ['unpaid', 'payment_pending'], true)
            && $order->order_date_local?->format('Y-m-d') === now('Asia/Jakarta')->toDateString();
    }

    public function openCancelModal(int $orderId): void
    {
        $this->cancelOrderId = $orderId;
        $this->showCancelModal = true;
    }

    public function resetCancelModal(): void
    {
        $this->reset(['cancelOrderId', 'cancelReason', 'showCancelModal']);
    }

    public function confirmCancelOrder(OrderService $orderService)
    {
        $order = $this->cancelOrderId ? Order::find($this->cancelOrderId) : null;

        if (! $order) {
            return;
        }

        if (! $this->canCancelToday($order)) {
            $this->resetCancelModal();
            session()->flash('error', 'Pembatalan manual hanya bisa dilakukan pada hari yang sama dengan tanggal transaksi.');

            return;
        }

        $this->validate([
            'cancelReason' => 'required|string|min:5',
        ]);

        $orderService->cancelOrder($order, $this->cancelReason, 'ADMIN', auth()->user());

        $this->resetCancelModal();
        session()->flash('success', "Order #{$order->order_number} berhasil dibatalkan dan stok telah direstore.");
    }

    public function openTerkendalaModal(int $orderId): void
    {
        $this->terkendalaOrderId = $orderId;
        $this->showTerkendalaModal = true;
    }

    public function resetTerkendalaModal(): void
    {
        $this->reset(['terkendalaOrderId', 'terkendalaReason', 'showTerkendalaModal']);
    }

    public function confirmTerkendala(FulfillmentService $fulfillment)
    {
        $order = $this->terkendalaOrderId ? Order::find($this->terkendalaOrderId) : null;

        if (! $order || $order->status !== 'shipped') {
            return;
        }

        $this->validate([
            'terkendalaReason' => 'required|string|min:5',
        ]);

        $fulfillment->markTerkendala($order, $this->terkendalaReason, auth()->user());

        $this->resetTerkendalaModal();
        session()->flash('success', "Order #{$order->order_number} ditandai terkendala pengiriman.");
    }

    public function render()
    {
        $query = Order::with(['user', 'items', 'invoice', 'latestPaymentProof', 'shipment'])->latest();

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
            'cancelOrder' => $this->cancelOrderId ? Order::find($this->cancelOrderId) : null,
            'terkendalaOrder' => $this->terkendalaOrderId ? Order::find($this->terkendalaOrderId) : null,
        ])->layout('components.layouts.app');
    }
}
