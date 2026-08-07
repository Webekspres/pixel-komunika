<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\InventoryLedger;
use App\Models\InventorySnapshot;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function createOrderFromCart(
        User $user,
        Cart $cart,
        Address $address,
        array $shippingOption
    ): Order {
        $summary = $this->cartService->getCartSummary($cart);

        if ($summary['items']->isEmpty()) {
            throw new InvalidArgumentException('Keranjang belanja kosong.');
        }

        $shippingCost = (float) ($shippingOption['cost'] ?? 0);
        $subtotal = (float) $summary['subtotal'];
        $taxPph22 = (float) $summary['pph22'];
        $grandTotal = $subtotal + $shippingCost + $taxPph22;

        return DB::transaction(function () use (
            $user,
            $cart,
            $address,
            $shippingOption,
            $summary,
            $subtotal,
            $taxPph22,
            $shippingCost,
            $grandTotal
        ) {
            $datePrefix = now()->format('Ymd');
            $randomSuffix = strtoupper(Str::random(5));
            $orderNumber = "PK-{$datePrefix}-{$randomSuffix}";
            $invoiceNumber = "INV-{$datePrefix}-{$randomSuffix}";

            // 1. Create Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => 'unpaid',
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->recipient_phone,
                'shipping_address_line' => $address->address_line,
                'shipping_province' => $address->province_name,
                'shipping_city' => $address->city_name,
                'shipping_district' => $address->district_name,
                'shipping_postal_code' => $address->postal_code,
                'courier_code' => $shippingOption['code'] ?? 'jne',
                'courier_service' => $shippingOption['service'] ?? 'REG',
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'tax_pph22' => $taxPph22,
                'grand_total' => $grandTotal,
                'expires_at' => now()->addHours(24),
            ]);

            // 2. Create Order Items & Reserve Inventory
            foreach ($summary['items'] as $itemData) {
                $product = $itemData['product'];
                $qty = $itemData['quantity'];
                $unitPrice = $itemData['unit_price'];
                $lineSubtotal = $itemData['line_subtotal'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineSubtotal,
                    'pph22_amount' => 0, // Item level PPh 22 can be expanded if needed
                ]);

                // Update inventory snapshot & record ledger entry
                $snapshot = InventorySnapshot::where('product_id', $product->id)->lockForUpdate()->first();
                if ($snapshot) {
                    $qtyBefore = $snapshot->quantity_available;
                    $qtyAfter = max(0, $qtyBefore - $qty);
                    
                    $status = InventorySnapshot::AVAILABLE;
                    if ($qtyAfter == 0) {
                        $status = InventorySnapshot::OUT;
                    } elseif ($qtyAfter <= $snapshot->low_stock_threshold) {
                        $status = InventorySnapshot::LOW;
                    }

                    $snapshot->update([
                        'quantity_available' => $qtyAfter,
                        'stock_status' => $status,
                    ]);

                    InventoryLedger::create([
                        'product_id' => $product->id,
                        'source' => 'ORDER_CREATED',
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyAfter,
                        'quantity_delta' => -$qty,
                        'external_reference' => $orderNumber,
                        'occurred_at' => now(),
                        'meta' => [
                            'order_id' => $order->id,
                            'customer_id' => $user->id,
                        ],
                    ]);
                }
            }

            // 3. Create Invoice Snapshot
            Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax_pph22' => $taxPph22,
                'shipping_cost' => $shippingCost,
                'amount' => $grandTotal,
                'status' => 'unpaid',
                'due_at' => now()->addHours(24),
            ]);

            // 4. Empty Cart
            $cart->items()->delete();

            return $order;
        });
    }

    /**
     * Cancel order and restore product stock.
     */
    public function cancelOrder(Order $order, string $reason, string $cancelledBy = 'SYSTEM'): void
    {
        if (in_array($order->status, ['cancelled', 'completed', 'returned'])) {
            throw new InvalidArgumentException("Order #{$order->order_number} tidak dapat dibatalkan.");
        }

        DB::transaction(function () use ($order, $reason, $cancelledBy) {
            $order->update(['status' => 'cancelled']);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'cancelled']);
            }

            // Restore stock for all order items
            foreach ($order->items as $item) {
                $snapshot = InventorySnapshot::where('product_id', $item->product_id)->lockForUpdate()->first();
                if ($snapshot) {
                    $qtyBefore = $snapshot->quantity_available;
                    $qtyAfter = $qtyBefore + $item->quantity;

                    $status = InventorySnapshot::AVAILABLE;
                    if ($qtyAfter <= $snapshot->low_stock_threshold) {
                        $status = InventorySnapshot::LOW;
                    }

                    $snapshot->update([
                        'quantity_available' => $qtyAfter,
                        'stock_status' => $status,
                    ]);

                    InventoryLedger::create([
                        'product_id' => $item->product_id,
                        'source' => 'ORDER_CANCELLED',
                        'quantity_before' => $qtyBefore,
                        'quantity_after' => $qtyAfter,
                        'quantity_delta' => $item->quantity,
                        'external_reference' => $order->order_number,
                        'occurred_at' => now(),
                        'meta' => [
                            'order_id' => $order->id,
                            'reason' => $reason,
                            'cancelled_by' => $cancelledBy,
                        ],
                    ]);
                }
            }
        });
    }

    /**
     * Request an order return from customer.
     */
    public function requestReturn(Order $order, User $user, string $reason): \App\Models\OrderReturn
    {
        if ($order->status !== 'completed' && $order->status !== 'shipped') {
            throw new InvalidArgumentException("Retur hanya dapat diajukan untuk order yang sudah dikirim atau selesai.");
        }

        return \App\Models\OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => $reason,
            'status' => 'requested',
        ]);
    }

    /**
     * Admin processes a return request (approve/reject).
     */
    public function processReturn(\App\Models\OrderReturn $return, string $action, ?float $refundAmount, User $admin, ?string $adminNotes = null): void
    {
        DB::transaction(function () use ($return, $action, $refundAmount, $admin, $adminNotes) {
            if ($action === 'approve') {
                $return->update([
                    'status' => 'approved',
                    'refund_amount' => $refundAmount ?? $return->order->grand_total,
                    'admin_notes' => $adminNotes,
                    'processed_by' => $admin->id,
                    'processed_at' => now(),
                ]);

                $return->order->update(['status' => 'returned']);

                if ($return->order->invoice) {
                    $return->order->invoice->update(['status' => 'refunded']);
                }

                // Restore stock
                foreach ($return->order->items as $item) {
                    $snapshot = InventorySnapshot::where('product_id', $item->product_id)->lockForUpdate()->first();
                    if ($snapshot) {
                        $qtyBefore = $snapshot->quantity_available;
                        $qtyAfter = $qtyBefore + $item->quantity;

                        $snapshot->update([
                            'quantity_available' => $qtyAfter,
                            'stock_status' => InventorySnapshot::AVAILABLE,
                        ]);

                        InventoryLedger::create([
                            'product_id' => $item->product_id,
                            'source' => 'ORDER_RETURNED',
                            'quantity_before' => $qtyBefore,
                            'quantity_after' => $qtyAfter,
                            'quantity_delta' => $item->quantity,
                            'external_reference' => $return->order->order_number,
                            'occurred_at' => now(),
                            'meta' => [
                                'return_id' => $return->id,
                                'admin_id' => $admin->id,
                            ],
                        ]);
                    }
                }
            } else {
                $return->update([
                    'status' => 'rejected',
                    'admin_notes' => $adminNotes,
                    'processed_by' => $admin->id,
                    'processed_at' => now(),
                ]);
            }
        });
    }
}
