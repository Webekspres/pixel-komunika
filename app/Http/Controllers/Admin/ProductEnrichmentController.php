<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Catalog\ProductEnrichmentService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductEnrichmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $categoryId = $request->integer('category');

        $products = Product::query()
            ->with(['category', 'brand', 'enrichment', 'prices', 'inventorySnapshot', 'media'])
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
            'product' => $product->load(['category', 'brand', 'enrichment', 'prices', 'inventorySnapshot', 'media']),
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

    public function storeMedia(Request $request, Product $product, ProductEnrichmentService $enrichment): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_primary' => ['sometimes', 'boolean'],
        ]);

        $file = $validated['image'];
        $path = $file->store('product-media', 'local');

        $enrichment->addMedia($product, [
            'media_type' => ProductMedia::IMAGE,
            'object_key' => $path,
            'alt_text' => $product->name,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_primary' => $request->boolean('is_primary'),
        ]);

        return back()->with('status', 'Gambar produk berhasil ditambahkan.');
    }

    public function showMedia(ProductMedia $media): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($media->object_key), 404);

        return Storage::disk('local')->response($media->object_key);
    }

    public function destroyMedia(ProductMedia $media, ProductEnrichmentService $enrichment): RedirectResponse
    {
        $productName = $media->product->name;
        Storage::disk('local')->delete($media->object_key);
        $enrichment->removeMedia($media);

        return back()->with('status', "Gambar untuk {$productName} berhasil dihapus.");
    }
}
