<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Keranjang Belanja - Pixel Komunika')]
class CartIndex extends Component
{
    public function updateQuantity(int $cartItemId, int $quantity, CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        try {
            $cartService->updateQuantity($cart, $cartItemId, $quantity);
            session()->flash('success', 'Keranjang berhasil diperbarui.');
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
        session()->flash('success', 'Item berhasil dihapus dari keranjang.');
    }

    public function clearCart(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        $cartService->clearCart($cart);
        $this->dispatch('cart-updated');
        session()->flash('success', 'Keranjang berhasil dikosongkan.');
    }

    public function render(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);
        $summary = $cartService->getCartSummary($cart);

        return view('livewire.storefront.cart-index', [
            'summary' => $summary,
        ])->layout('layouts.storefront');
    }
}
