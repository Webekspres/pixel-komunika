<?php

namespace App\Livewire\Pages;

use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pixel Komunika - E-Commerce Aksesoris & Konektivitas')]
class Home extends Component
{
    public string $selectedCategory = 'all';

    public string $search = '';

    public function addToCart(int $productId, CartService $cartService)
    {
        $user = Auth::user();

        // FR-CART-001: hanya pelanggan aktif yang dapat menambahkan ke keranjang.
        if (! $user) {
            session()->flash('error', 'Silakan masuk terlebih dahulu untuk menambahkan produk ke keranjang.');

            return redirect()->route('login');
        }

        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        try {
            $cartService->addItem($cart, $productId, 1);
            $productName = Product::query()->with('enrichment')->find($productId)?->displayName() ?? 'Produk';
            $this->dispatch('cart-updated');
            $this->dispatch('cart-item-added', name: $productName);
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

        $categories = Category::all();

        $query = Product::with(['category', 'enrichment', 'prices', 'inventorySnapshot', 'media.library']);

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        $products = $query->take(12)->get();

        return view('livewire.pages.home', [
            'categories' => $categories,
            'products' => $products,
            'cartCount' => $cartSummary['total_items'],
        ])->layout('layouts.storefront');
    }
}
