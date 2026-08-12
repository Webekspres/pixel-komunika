<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class PosOrderController extends Controller
{
    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::query()
            ->with(['items', 'invoice', 'user'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'subtotal' => (float) $order->subtotal,
            'tax_pph22' => (float) $order->tax_pph22,
            'tax_pph22_snapshot' => $order->tax_pph22_snapshot,
            'shipping_cost' => (float) $order->shipping_cost,
            'grand_total' => (float) $order->grand_total,
            'customer' => [
                'name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ],
            'invoice' => $order->invoice ? [
                'invoice_number' => $order->invoice->invoice_number,
                'status' => $order->invoice->status,
                'store_name' => $order->invoice->store_name,
                'store_npwp' => $order->invoice->store_npwp,
                'amount' => (float) $order->invoice->amount,
            ] : null,
            'items' => $order->items->map(fn ($item) => [
                'sku' => $item->sku,
                'product_name' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'subtotal' => (float) $item->subtotal,
            ])->values(),
            'acknowledged_at' => now()->toIso8601String(),
        ]);
    }
}
