<?php

namespace App\Domains\Pricing;

use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Collection;

class PriceCalculator
{
    public function resolvePrice(Product $product, int $quantity, bool $cartHasPartaiEligibleSku = false): ProductPrice
    {
        $prices = $product->prices->keyBy('price_type');

        $wholesale = $prices->get(ProductPrice::WHOLESALE);
        if ($wholesale && $quantity >= (int) $wholesale->minimum_quantity) {
            return $wholesale;
        }

        if ($cartHasPartaiEligibleSku && $prices->has(ProductPrice::BULK)) {
            return $prices->get(ProductPrice::BULK);
        }

        return $prices->get(ProductPrice::BULK, $prices->get(ProductPrice::RETAIL));
    }

    public function calculatePph22(Collection $lines): array
    {
        $components = $lines
            ->groupBy('category_id')
            ->map(function (Collection $group, int $categoryId): ?array {
                $rule = CategoryTaxRule::query()
                    ->where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->first();

                if (! $rule) {
                    return null;
                }

                $subtotal = $group->sum('line_total');

                if ($subtotal <= (float) $rule->threshold_amount) {
                    return null;
                }

                $amount = round($subtotal * ((float) $rule->rate_percent / 100), 2);

                return [
                    'category_id' => $categoryId,
                    'subtotal' => $subtotal,
                    'threshold_amount' => (float) $rule->threshold_amount,
                    'rate_percent' => (float) $rule->rate_percent,
                    'amount' => $amount,
                ];
            })
            ->filter()
            ->values();

        return [
            'total' => $components->sum('amount'),
            'components' => $components->all(),
        ];
    }
}
