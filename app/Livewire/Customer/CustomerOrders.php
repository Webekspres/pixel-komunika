<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Riwayat Pesanan - Pixel Komunika')]
class CustomerOrders extends Component
{
    public function render()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items', 'invoice'])
            ->latest()
            ->paginate(10);

        return view('livewire.customer.customer-orders', [
            'orders' => $orders,
        ])->layout('layouts.app');
    }
}
