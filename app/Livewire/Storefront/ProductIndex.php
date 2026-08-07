<?php

namespace App\Livewire\Storefront;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Katalog Produk - Pixel Komunika')]
class ProductIndex extends Component
{
    use WithPagination;

    #[Url(as: 'kategori')]
    public string $selectedCategory = 'all';

    #[Url(as: 'brand')]
    public string $selectedBrand = 'all';

    #[Url(as: 'cari')]
    public string $search = '';

    #[Url(as: 'urutan')]
    public string $sort = 'newest';

    #[Url(as: 'stok')]
    public bool $inStockOnly = false;

    #[Url(as: 'min_harga')]
    public ?string $minPrice = null;

    #[Url(as: 'max_harga')]
    public ?string $maxPrice = null;

    public bool $mobileFilterOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function updatingSelectedBrand()
    {
        $this->resetPage();
    }

    public function updatingSort()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['selectedCategory', 'selectedBrand', 'search', 'sort', 'inStockOnly', 'minPrice', 'maxPrice']);
        $this->resetPage();
    }

    public function addToCart(int $productId, CartService $cartService)
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);

        try {
            $cartService->addItem($cart, $productId, 1);
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart-drawer');
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render(CartService $cartService)
    {
        $categories = Category::all();
        $brands = Brand::all();

        $query = Product::with(['category', 'brand', 'enrichment', 'latestPrice', 'inventorySnapshot']);

        if ($this->selectedCategory !== 'all') {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->selectedBrand !== 'all') {
            $query->where('brand_id', $this->selectedBrand);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        if ($this->inStockOnly) {
            $query->whereHas('inventorySnapshot', function ($q) {
                $q->where('quantity_available', '>', 0);
            });
        }

        if ($this->minPrice !== null && is_numeric($this->minPrice)) {
            $query->whereHas('latestPrice', function ($q) {
                $q->where('price_wholesale_tier1', '>=', (float)$this->minPrice);
            });
        }

        if ($this->maxPrice !== null && is_numeric($this->maxPrice)) {
            $query->whereHas('latestPrice', function ($q) {
                $q->where('price_wholesale_tier1', '<=', (float)$this->maxPrice);
            });
        }

        // Sorting
        match ($this->sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'price_low' => $query->join('product_prices', 'products.id', '=', 'product_prices.product_id')
                                 ->orderBy('product_prices.price_wholesale_tier1', 'asc')
                                 ->select('products.*'),
            'price_high' => $query->join('product_prices', 'products.id', '=', 'product_prices.product_id')
                                  ->orderBy('product_prices.price_wholesale_tier1', 'desc')
                                  ->select('products.*'),
            default => $query->latest(),
        };

        $products = $query->paginate(12);

        $user = Auth::user();
        $sessionId = session()->getId();
        $cart = $cartService->getOrCreateCart($user, $sessionId);
        $cartSummary = $cartService->getCartSummary($cart);

        return view('livewire.storefront.product-index', [
            'categories' => $categories,
            'brands' => $brands,
            'products' => $products,
            'cartCount' => $cartSummary['total_items'],
        ])->layout('layouts.guest');
    }
}
