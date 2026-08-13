<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
    #[On('cart-updated')]
    public function handleCartUpdated(): void
    {
        // Re-render picks up the latest cart summary.
    }

    public function render(CartService $cartService)
    {
        $cart = $cartService->getOrCreateCart(Auth::user(), session()->getId());
        $summary = $cartService->getCartSummary($cart);

        return view('livewire.storefront.cart-badge', [
            'cartCount' => (int) ($summary['total_items'] ?? 0),
        ]);
    }
}
