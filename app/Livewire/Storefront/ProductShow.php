<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;
    public int $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'brand', 'enrichment', 'latestPrice', 'inventorySnapshot']);
    }

    public function incrementQuantity()
    {
        $maxStock = $this->product->inventorySnapshot?->quantity_available ?? 999;
        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        try {
            $cartService->addItem($cart, $this->product->id, $this->quantity);
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart-drawer');
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);
        $cartSummary = $cartService->getCartSummary($cart);

        // Fetch related products in same category
        $relatedProducts = Product::with(['category', 'latestPrice', 'inventorySnapshot'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(4)
            ->get();

        return view('livewire.storefront.product-show', [
            'relatedProducts' => $relatedProducts,
            'cartCount' => $cartSummary['total_items'],
        ])
        ->layout('layouts.guest')
        ->title("{$this->product->name} - Pixel Komunika");
    }
}
