<?php

namespace App\Services;

use App\Domains\Pricing\PriceCalculator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use InvalidArgumentException;

class CartService
{
    public function __construct(
        protected PriceCalculator $priceCalculator
    ) {}

    public function getOrCreateCart(?User $user, ?string $sessionId = null): Cart
    {
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);

            // If session cart exists with items, merge session items to user cart
            if ($sessionId) {
                $sessionCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
                if ($sessionCart) {
                    foreach ($sessionCart->items as $sItem) {
                        $this->addItem($cart, $sItem->product_id, $sItem->quantity);
                    }
                    $sessionCart->delete();
                }
            }

            return $cart;
        }

        if ($sessionId) {
            return Cart::firstOrCreate(['session_id' => $sessionId]);
        }

        throw new InvalidArgumentException('User or Session ID required to resolve cart.');
    }

    public function addItem(Cart $cart, int $productId, int $quantity = 1): CartItem
    {
        $product = Product::with('inventorySnapshot')->findOrFail($productId);

        $stockAvailable = $product->inventorySnapshot ? $product->inventorySnapshot->quantity_available : 0;

        $existingItem = $cart->items()->where('product_id', $productId)->first();
        $newQuantity = $existingItem ? ($existingItem->quantity + $quantity) : $quantity;

        if ($newQuantity > $stockAvailable) {
            throw new InvalidArgumentException("Stok tidak mencukupi. Stok tersedia: {$stockAvailable}");
        }

        if ($existingItem) {
            $existingItem->update(['quantity' => $newQuantity]);

            return $existingItem;
        }

        return $cart->items()->create([
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    public function updateQuantity(Cart $cart, int $cartItemId, int $quantity): ?CartItem
    {
        $cartItem = $cart->items()->with('product.inventorySnapshot')->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $cartItem->delete();

            return null;
        }

        $stockAvailable = $cartItem->product->inventorySnapshot ? $cartItem->product->inventorySnapshot->quantity_available : 0;
        if ($quantity > $stockAvailable) {
            throw new InvalidArgumentException("Stok tidak mencukupi. Stok tersedia: {$stockAvailable}");
        }

        $cartItem->update(['quantity' => $quantity]);

        return $cartItem;
    }

    public function removeItem(Cart $cart, int $cartItemId): bool
    {
        return (bool) $cart->items()->where('id', $cartItemId)->delete();
    }

    public function getCartSummary(Cart $cart): array
    {
        $cart->load(['items.product.category', 'items.product.prices']);

        $items = $cart->items;
        if ($items->isEmpty()) {
            return [
                'items' => collect(),
                'subtotal' => 0,
                'pph22' => 0,
                'pph22_components' => [],
                'total_weight_grams' => 0,
                'total_items' => 0,
            ];
        }

        // Determine if any item in cart qualifies for partai (min qty per SKU, not aggregated)
        $hasPartaiEligible = false;
        foreach ($items as $item) {
            $productPrices = $item->product->prices->keyBy('price_type');
            $bulkPrice = $productPrices->get(ProductPrice::BULK);
            $minQty = (int) ($bulkPrice?->minimum_quantity ?? 5);
            if ($bulkPrice && $item->quantity >= $minQty) {
                $hasPartaiEligible = true;
                break;
            }
        }

        $subtotal = 0;
        $totalWeightGrams = 0;
        $processedLines = collect();
        $itemSummaries = collect();

        foreach ($items as $item) {
            $priceModel = $this->priceCalculator->resolvePrice($item->product, $item->quantity, $hasPartaiEligible);
            $unitPrice = $priceModel ? (float) $priceModel->amount : 0;
            $lineSubtotal = round($unitPrice * $item->quantity, 2);

            $subtotal += $lineSubtotal;
            $itemWeight = ($item->product->weight_grams ?? 500) * $item->quantity;
            $totalWeightGrams += $itemWeight;

            $processedLines->push([
                'category_id' => $item->product->category_id,
                'line_total' => $lineSubtotal,
            ]);

            $itemSummaries->push([
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'price_type' => $priceModel ? $priceModel->price_type : 'RETAIL',
                'line_subtotal' => $lineSubtotal,
            ]);
        }

        $pph22Result = $this->priceCalculator->calculatePph22($processedLines);

        return [
            'items' => $itemSummaries,
            'subtotal' => $subtotal,
            'pph22' => $pph22Result['total'],
            'pph22_components' => $pph22Result['components'],
            'total_weight_grams' => $totalWeightGrams,
            'total_items' => $items->sum('quantity'),
        ];
    }
}
