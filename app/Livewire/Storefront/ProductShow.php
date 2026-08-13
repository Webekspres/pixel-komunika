<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductShow extends Component
{
    public Product $product;

    public int $quantity = 1;

    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'brand', 'enrichment', 'prices', 'inventorySnapshot']);
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
            $this->dispatch('cart-item-added', name: $this->product->name);
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
        $relatedProducts = Product::with(['category', 'prices', 'inventorySnapshot'])
            ->where('category_id', $this->product->category_id)
            ->where('id', '!=', $this->product->id)
            ->take(4)
            ->get();

        return view('livewire.storefront.product-show', [
            'relatedProducts' => $relatedProducts,
            'cartCount' => $cartSummary['total_items'],
        ])
            ->layout('layouts.storefront')
            ->title("{$this->product->name} - Pixel Komunika");
    }
}
