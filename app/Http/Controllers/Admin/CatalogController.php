<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function categories(): View
    {
        return view('admin.categories.index', [
            'categories' => Category::query()
                ->withCount('products')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function toggleCategory(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $category->update(['is_active' => $validated['is_active']]);

        return back()->with('status', "Status kategori {$category->name} diperbarui.");
    }

    public function brands(): View
    {
        return view('admin.brands.index', [
            'brands' => Brand::query()
                ->withCount('products')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function toggleBrand(Request $request, Brand $brand): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $brand->update(['is_active' => $validated['is_active']]);

        return back()->with('status', "Status merek {$brand->name} diperbarui.");
    }
}
