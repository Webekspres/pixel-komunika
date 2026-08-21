<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Pesanan Saya - Pixel Komunika')]
class CustomerOrders extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $status = 'all';

    public function setFilter(string $status): void
    {
        $this->status = $status;
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $query = Order::where('user_id', $user->id)
            ->with(['items.product', 'invoice', 'shipment']);

        if ($this->status === 'unpaid') {
            $query->whereIn('status', ['unpaid', 'payment_pending', 'payment_rejected']);
        } elseif ($this->status === 'processing') {
            $query->whereIn('status', ['paid', 'processing', 'packed']);
        } elseif ($this->status === 'shipped') {
            $query->where('status', 'shipped');
        } elseif ($this->status === 'completed') {
            $query->where('status', 'completed');
        } elseif ($this->status === 'cancelled') {
            $query->whereIn('status', ['cancelled', 'returned']);
        }

        $orders = $query->latest()->paginate(10);

        $counts = [
            'all' => Order::where('user_id', $user->id)->count(),
            'unpaid' => Order::where('user_id', $user->id)->whereIn('status', ['unpaid', 'payment_pending', 'payment_rejected'])->count(),
            'processing' => Order::where('user_id', $user->id)->whereIn('status', ['paid', 'processing', 'packed'])->count(),
            'shipped' => Order::where('user_id', $user->id)->where('status', 'shipped')->count(),
            'completed' => Order::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Order::where('user_id', $user->id)->whereIn('status', ['cancelled', 'returned'])->count(),
        ];

        return view('livewire.customer.customer-orders', [
            'orders' => $orders,
            'counts' => $counts,
        ])->layout('components.layouts.customer');
    }
}
