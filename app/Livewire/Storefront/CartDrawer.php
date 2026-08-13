<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class CartDrawer extends Component
{
    public bool $isOpen = false;

    #[On('open-cart-drawer')]
    public function openDrawer()
    {
        $this->isOpen = true;
    }

    #[On('cart-updated')]
    public function handleCartUpdated()
    {
        // Triggers re-render automatically
    }

    public function closeDrawer()
    {
        $this->isOpen = false;
    }

    public function updateQuantity(int $cartItemId, int $quantity, CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        try {
            $cartService->updateQuantity($cart, $cartItemId, $quantity);
            $this->dispatch('cart-updated');
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function removeItem(int $cartItemId, CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        $cartService->removeItem($cart, $cartItemId);
        $this->dispatch('cart-updated');
    }

    public function clearCart(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        $cartService->clearCart($cart);
        $this->dispatch('cart-updated');
    }

    public function render(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);
        $summary = $cartService->getCartSummary($cart);

        return view('livewire.storefront.cart-drawer', [
            'summary' => $summary,
        ]);
    }
}
