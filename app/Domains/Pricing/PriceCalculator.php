<?php

namespace App\Domains\Pricing;

use App\Models\CategoryTaxRule;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\StoreProfile;
use Illuminate\Support\Collection;

class PriceCalculator
{
    public function resolvePrice(Product $product, int $quantity, bool $cartHasPartaiEligibleSku = false): ProductPrice
    {
        $prices = $product->prices->keyBy('price_type');
        $partaiMin = $this->partaiMinimumQuantity();

        $wholesale = $prices->get(ProductPrice::WHOLESALE);
        $bulk = $prices->get(ProductPrice::BULK);

        // Partai beats grosir once any SKU in cart meets global partai minimum.
        if ($cartHasPartaiEligibleSku && $bulk) {
            return $bulk;
        }

        if ($bulk && $quantity >= $partaiMin) {
            return $bulk;
        }

        if ($wholesale && $quantity >= (int) $wholesale->minimum_quantity) {
            return $wholesale;
        }

        return $prices->get(ProductPrice::RETAIL, $bulk ?? $wholesale);
    }

    public function partaiMinimumQuantity(): int
    {
        $fromStore = StoreProfile::active()?->partai_minimum_quantity;

        return (int) ($fromStore ?: 5);
    }

    /**
     * MVP-017: sum triggered category subtots, then (basis / 1.11) × rate.
     *
     * @return array{total: float, components: list<array>, aggregate: ?array}
     */
    public function calculatePph22(Collection $lines): array
    {
        $triggered = $lines
            ->groupBy('category_id')
            ->map(function (Collection $group, int|string $categoryId): ?array {
                $categoryId = (int) $categoryId;
                $rule = CategoryTaxRule::query()
                    ->where('category_id', $categoryId)
                    ->where('is_active', true)
                    ->first();

                if (! $rule || (float) $rule->rate_percent <= 0) {
                    return null;
                }

                $subtotal = (float) $group->sum('line_total');

                if ($subtotal <= (float) $rule->threshold_amount) {
                    return null;
                }

                return [
                    'category_id' => $categoryId,
                    'category_tax_rule_id' => $rule->id,
                    'subtotal' => $subtotal,
                    'threshold_amount' => (float) $rule->threshold_amount,
                    'rate_percent' => (float) $rule->rate_percent,
                ];
            })
            ->filter()
            ->values();

        if ($triggered->isEmpty()) {
            return [
                'total' => 0.0,
                'components' => [],
                'aggregate' => null,
            ];
        }

        $basis = (float) $triggered->sum('subtotal');
        $rate = $this->resolveAggregateRate($triggered);
        $divisor = (float) config('store.pph22.divisor', 1.11);
        $amount = $this->roundMoney(($basis / $divisor) * ($rate / 100));

        $aggregate = [
            'component_code' => 'PPH22',
            'label_snapshot' => 'PPh 22',
            'basis_amount' => $basis,
            'divisor' => $divisor,
            'rate_percent' => $rate,
            'amount' => $amount,
            'config_snapshot' => [
                'calculation_basis' => 'TRIGGERED_SUBTOTAL_DIV_1_11',
                'categories' => $triggered->all(),
                'rounding' => config('store.pph22.rounding', 'half_up'),
            ],
            'category_tax_rule_id' => $triggered->count() === 1
                ? $triggered->first()['category_tax_rule_id']
                : null,
        ];

        return [
            'total' => $amount,
            'components' => $triggered->map(fn (array $row) => [
                ...$row,
                'amount' => $amount, // display hint; invoice uses aggregate
            ])->all(),
            'aggregate' => $aggregate,
        ];
    }

    protected function resolveAggregateRate(Collection $triggered): float
    {
        $rates = $triggered->pluck('rate_percent')->unique()->values();

        if ($rates->count() === 1) {
            return (float) $rates->first();
        }

        // ponytail: OPN-006 — provisional multi-rate strategy
        return config('store.pph22.multi_category_rate_strategy', 'max') === 'first'
            ? (float) $rates->first()
            : (float) $rates->max();
    }

    protected function roundMoney(float $value): float
    {
        return match (config('store.pph22.rounding', 'half_up')) {
            'floor' => floor($value * 100) / 100,
            'ceil' => ceil($value * 100) / 100,
            default => round($value, 2),
        };
    }
}
