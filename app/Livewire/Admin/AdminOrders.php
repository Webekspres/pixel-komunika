<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Admin - Manajemen Pesanan')]
class AdminOrders extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';
    public string $search = '';

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

    public function render()
    {
        $query = Order::with(['user', 'items', 'invoice'])->latest();

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
        ])->layout('layouts.app');
    }
}
