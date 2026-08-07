<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Pesanan - Pixel Komunika')]
class OrderDetail extends Component
{
    public Order $order;

    public function mount(Order $order)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && $order->user_id !== $user->id) {
            abort(403);
        }

        $this->order = $order->load(['items', 'invoice', 'user']);
    }

    public function render()
    {
        return view('livewire.customer.order-detail')->layout('layouts.app');
    }
}
