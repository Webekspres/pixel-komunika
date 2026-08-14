<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Catalog\ProductEnrichmentService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductEnrichmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $categoryId = $request->integer('category');

        $products = Product::query()
            ->with(['category', 'brand', 'enrichment', 'prices', 'inventorySnapshot', 'media.library'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('sku', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::query()->orderBy('name')->get(),
            'search' => $search,
            'selectedCategory' => $categoryId,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product->load(['category', 'brand', 'enrichment', 'prices', 'inventorySnapshot', 'media.library']),
            'categories' => Category::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product, ProductEnrichmentService $enrichment): RedirectResponse
    {
        $validated = $request->validate([
            'display_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('product_enrichments', 'slug')->ignore($product->enrichment?->id)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:191'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'label' => ['nullable', 'string', 'max:100'],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'is_visible' => ['sometimes', 'boolean'],
        ]);

        $enrichment->upsertEnrichment($product, [
            ...$validated,
            'is_visible' => $request->boolean('is_visible'),
        ]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', "Presentasi produk {$product->name} berhasil disimpan.");
    }
}
