<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryTaxRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryTaxRuleController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('taxRule')
            ->orderBy('name')
            ->get();

        return view('admin.tax-rules.index', [
            'categories' => $categories,
        ]);
    }

    public function edit(Category $category): View
    {
        $taxRule = $category->taxRule ?? new CategoryTaxRule([
            'category_id' => $category->id,
            'threshold_amount' => 0,
            'rate_percent' => 0,
            'is_active' => true,
        ]);

        return view('admin.tax-rules.edit', [
            'category' => $category,
            'taxRule' => $taxRule,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'threshold_amount' => ['required', 'numeric', 'min:0'],
            'rate_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        CategoryTaxRule::query()->updateOrCreate(
            ['category_id' => $category->id],
            [
                'threshold_amount' => $validated['threshold_amount'],
                'rate_percent' => $validated['rate_percent'],
                'is_active' => $request->boolean('is_active'),
            ],
        );

        return redirect()
            ->route('admin.tax-rules.index')
            ->with('status', "Aturan PPh 22 untuk {$category->name} berhasil disimpan.");
    }
}
