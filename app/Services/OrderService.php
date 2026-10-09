<?php

namespace App\Services;

use App\Domains\Audit\AuditLogger;
use App\Domains\Notifications\NotificationService;
use App\Domains\PosIntegration\PosOutboxService;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\InventoryLedger;
use App\Models\InventorySnapshot;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderChargeComponent;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\Payment;
use App\Models\SalesReturn;
use App\Models\Shipment;
use App\Models\StoreProfile;
use App\Models\User;
use App\Services\Shipping\StoreCourierFreeShipping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected NotificationService $notifications,
        protected PosOutboxService $posOutbox,
        protected AuditLogger $audit,
    ) {}

    public function createOrderFromCart(
        User $user,
        Cart $cart,
        Address $address,
        array $shippingOption,
        ?string $idempotencyKey = null,
    ): Order {
        if (! $user->isActiveCustomer()) {
            throw new InvalidArgumentException('Akun Anda tidak aktif untuk melakukan pemesanan.');
        }

        $summary = $this->cartService->getCartSummary($cart);

        if ($blocker = $this->cartService->checkoutBlocker($summary)) {
            throw new InvalidArgumentException($blocker);
        }

        if ($idempotencyKey) {
            $existing = Order::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }
        }

        $shippingOption = StoreCourierFreeShipping::applyToOption(
            $shippingOption,
            (float) $summary['subtotal'],
            (float) $summary['pph22'],
        );

        $shippingCost = (float) ($shippingOption['cost'] ?? 0);
        $subtotal = (float) $summary['subtotal'];
        $taxPph22 = (float) $summary['pph22'];
        $taxPph22Snapshot = $summary['pph22_components'] ?? [];
        $pphAggregate = $summary['pph22_aggregate'] ?? null;
        $grandTotal = $subtotal + $shippingCost + $taxPph22;
        $store = StoreProfile::active();
        $bank = BankAccount::defaultActive();

        return DB::transaction(function () use (
            $user,
            $cart,
            $address,
            $shippingOption,
            $summary,
            $subtotal,
            $taxPph22,
            $taxPph22Snapshot,
            $pphAggregate,
            $shippingCost,
            $grandTotal,
            $idempotencyKey,
            $store,
            $bank,
        ) {
            // Kunci keranjang: dua submit bersamaan (dua tab) tidak membuat dua order.
            $lockedCart = Cart::query()->lockForUpdate()->find($cart->id);
            if (! $lockedCart || $lockedCart->items()->doesntExist()) {
                throw new InvalidArgumentException('Keranjang belanja kosong.');
            }

            $datePrefix = now()->format('Ymd');
            $randomSuffix = strtoupper(Str::random(5));
            $orderNumber = "PK-{$datePrefix}-{$randomSuffix}";
            // CR-023: nomor invoice web terpisah dari POS, WEB-[yymm]-[0001], urut ulang tiap bulan.
            // ponytail: lockForUpdate menahan checkout bersamaan; deadlock sisa (awal bulan, belum ada baris) diulang
            // lewat attempts transaksi, unique index sebagai pengaman akhir. Tambah tabel counter bila volume besar.
            $invoicePrefix = 'WEB-'.now()->format('ym').'-';
            $lastInvoiceNumber = Invoice::query()
                ->where('invoice_number', 'like', $invoicePrefix.'%')
                ->orderByRaw('LENGTH(invoice_number) DESC')
                ->orderByDesc('invoice_number')
                ->lockForUpdate()
                ->value('invoice_number');
            $invoiceNumber = $invoicePrefix.str_pad((string) ((int) substr((string) $lastInvoiceNumber, strlen($invoicePrefix)) + 1), 4, '0', STR_PAD_LEFT);

            $order = Order::create([
                'order_number' => $orderNumber,
                'idempotency_key' => $idempotencyKey ?: (string) Str::uuid(),
                'user_id' => $user->id,
                'address_id' => $address->id,
                'status' => 'unpaid',
                'order_date_local' => now('Asia/Jakarta')->toDateString(),
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
                'tax_pph22_snapshot' => $taxPph22Snapshot,
                'grand_total' => $grandTotal,
                'expires_at' => now('Asia/Jakarta')->endOfDay(),
            ]);

            foreach ($summary['items'] as $itemData) {
                $product = $itemData['product'];
                $qty = $itemData['quantity'];

                if ($qty < 1 || ! $product->isStorefrontVisible()) {
                    throw new InvalidArgumentException("Produk {$product->displayName()} sudah tidak tersedia. Hapus dari keranjang untuk melanjutkan.");
                }
                $unitPrice = $itemData['unit_price'];
                $lineSubtotal = $itemData['line_subtotal'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'category_id_snapshot' => $product->category_id,
                    'category_name_snapshot' => $product->category?->name ?? 'Unknown',
                    'product_name' => $product->displayName(),
                    'sku' => $product->sku,
                    'price_type' => $itemData['price_type'] ?? null,
                    'unit_price' => $unitPrice,
                    'quantity' => $qty,
                    'subtotal' => $lineSubtotal,
                    'pph22_amount' => 0,
                ]);

                $snapshot = InventorySnapshot::where('product_id', $product->id)->lockForUpdate()->first();
                if ($snapshot) {
                    $qtyBefore = $snapshot->quantity_available;
                    // FR-CART-004: stok berubah saat checkout -> checkout dihentikan (rollback).
                    if ($qtyBefore < $qty) {
                        throw new InvalidArgumentException("Stok {$product->displayName()} tidak mencukupi (tersisa {$qtyBefore}). Silakan perbarui keranjang.");
                    }
                    $qtyAfter = $qtyBefore - $qty;

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

            if ($pphAggregate) {
                OrderChargeComponent::create([
                    'order_id' => $order->id,
                    'category_tax_rule_id' => $pphAggregate['category_tax_rule_id'] ?? null,
                    'component_code' => OrderChargeComponent::PPH22,
                    'label_snapshot' => $pphAggregate['label_snapshot'],
                    'basis_amount' => $pphAggregate['basis_amount'],
                    'divisor' => $pphAggregate['divisor'],
                    'rate_percent' => $pphAggregate['rate_percent'],
                    'amount' => $pphAggregate['amount'],
                    'config_snapshot' => $pphAggregate['config_snapshot'],
                    'created_at' => now(),
                ]);
            }

            $provider = ($shippingOption['provider'] ?? null) === Shipment::PROVIDER_STORE
                ? Shipment::PROVIDER_STORE
                : Shipment::PROVIDER_BITESHIP;

            Shipment::create([
                'order_id' => $order->id,
                'store_courier_rate_id' => $shippingOption['store_courier_rate_id'] ?? null,
                'rate_provider' => $provider,
                'courier_code' => $shippingOption['code'] ?? null,
                'courier_name_snapshot' => $shippingOption['name'] ?? $shippingOption['service'] ?? 'Kurir',
                'service_code' => $shippingOption['service'] ?? null,
                'service_name_snapshot' => $shippingOption['service'] ?? $shippingOption['name'] ?? 'Standard',
                'eta_snapshot' => $shippingOption['etd'] ?? null,
                'recipient_name_snapshot' => $address->recipient_name,
                'recipient_phone_snapshot' => $address->recipient_phone,
                'address_snapshot' => $address->address_line,
                'province_snapshot' => $address->province_name,
                'city_snapshot' => $address->city_name,
                'district_snapshot' => $address->district_name,
                'postal_code_snapshot' => $address->postal_code,
                'shipping_amount' => $shippingCost,
                'rate_request_hash' => $shippingOption['rate_request_hash'] ?? null,
                'quoted_at' => now(),
                'shipment_group_code' => $this->shipmentGroupCode($user->id, $address),
                'status' => 'PROCESSING',
                'issue_status' => Shipment::ISSUE_NONE,
            ]);

            if ($bank) {
                Payment::create([
                    'order_id' => $order->id,
                    'bank_account_id' => $bank->id,
                    'status' => Payment::NOT_SUBMITTED,
                    'amount' => $grandTotal,
                ]);
            }

            Invoice::create([
                'invoice_number' => $invoiceNumber,
                'order_id' => $order->id,
                'store_profile_id' => $store?->id,
                'user_id' => $user->id,
                'store_name' => $store?->store_name ?? config('store.name'),
                'store_address' => $store?->address ?? config('store.address'),
                'store_phone' => $store?->contact_number ?? config('store.phone'),
                'store_npwp' => $store?->company_npwp ?? config('store.npwp'),
                'company_name_snapshot' => $store?->company_name ?? config('store.company_name'),
                'reseller_account_number_snapshot' => $user->customerProfile?->reseller_account_number ?? '',
                'subtotal' => $subtotal,
                'tax_pph22' => $taxPph22,
                'shipping_cost' => $shippingCost,
                'amount' => $grandTotal,
                'status' => 'unpaid',
                'due_at' => $order->expires_at,
            ]);

            $cart->items()->delete();

            $this->notifications->notifyNewOrder($order);

            return $order->fresh(['items', 'invoice', 'shipment', 'payment', 'chargeComponents']);
        }, attempts: 3); // Deadlock nomor invoice (dua checkout bersamaan di awal bulan) diulang otomatis.
    }

    /**
     * @param  list<string>  $onlyFrom  status yang boleh dibatalkan, dicek ulang setelah lock
     */
    public function cancelOrder(
        Order $order,
        string $reason,
        string $cancelledBy = 'SYSTEM',
        ?User $actor = null,
        array $onlyFrom = ['unpaid', 'payment_pending'],
    ): void {
        $source = strtoupper($cancelledBy) === 'ADMIN' ? 'ADMIN' : 'SYSTEM';

        DB::transaction(function () use ($order, $reason, $source, $actor, $onlyFrom) {
            // Lock + cek ulang agar admin cancel dan auto-cancel yang bersamaan
            // tidak mengembalikan stok dua kali.
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);
            if (! in_array($order->status, $onlyFrom, true)) {
                throw new InvalidArgumentException("Order #{$order->order_number} tidak dapat dibatalkan.");
            }

            $order->update([
                'status' => 'cancelled',
                'cancellation_source' => $source,
                'cancelled_by_user_id' => $source === 'ADMIN' ? $actor?->id : null,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            if ($order->invoice) {
                $order->invoice->update(['status' => 'cancelled']);
            }

            // Bukti yang masih menunggu review tidak boleh disetujui setelah batal (FR-ORD-008).
            $order->paymentProofs()->where('status', 'pending')->update([
                'status' => 'rejected',
                'rejection_reason' => 'Pesanan dibatalkan.',
                'is_active' => false,
            ]);

            foreach ($order->items as $item) {
                $this->restoreStock($item->product_id, $item->quantity, 'ORDER_CANCELLED', $order->order_number, [
                    'order_id' => $order->id,
                    'reason' => $reason,
                    'cancelled_by' => $source,
                ]);
            }

            // CR-023: pesanan dilaporkan ke POS baru setelah diproses, sedangkan pembatalan hanya
            // berlaku sebelum diproses; tidak ada penjualan POS yang perlu diretur.
            $this->audit->log('ORDER_CANCELLED', $order, $actor, null, [
                'cancellation_source' => $source,
                'reason' => $reason,
            ]);
        });
    }

    public function requestReturn(Order $order, User $user, string $reason): OrderReturn
    {
        if ($order->status !== 'completed' && $order->status !== 'shipped') {
            throw new InvalidArgumentException('Retur hanya dapat diajukan untuk order yang sudah dikirim atau selesai.');
        }

        return OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => $reason,
            'status' => 'requested',
        ]);
    }

    public function processReturn(OrderReturn $return, string $action, ?float $refundAmount, User $admin, ?string $adminNotes = null): void
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

                foreach ($return->order->items as $item) {
                    $this->restoreStock($item->product_id, $item->quantity, 'ORDER_RETURNED', $return->order->order_number, [
                        'return_id' => $return->id,
                        'admin_id' => $admin->id,
                    ]);
                }

                $salesReturn = SalesReturn::query()->updateOrCreate(
                    ['order_id' => $return->order_id],
                    [
                        'return_number' => 'SR-'.$return->order->order_number,
                        'reason' => $return->reason,
                        'reporting_status' => SalesReturn::PENDING,
                        'returned_at' => now(),
                    ],
                );

                $this->posOutbox->queueReturnReport($return->order, $salesReturn);
                $this->audit->log('ORDER_RETURN_APPROVED', $return, $admin);
            } else {
                $return->update([
                    'status' => 'rejected',
                    'admin_notes' => $adminNotes,
                    'processed_by' => $admin->id,
                    'processed_at' => now(),
                ]);
                $this->audit->log('ORDER_RETURN_REJECTED', $return, $admin);
            }
        });
    }

    protected function restoreStock(int $productId, int $qty, string $source, string $reference, array $meta): void
    {
        $snapshot = InventorySnapshot::where('product_id', $productId)->lockForUpdate()->first();
        if (! $snapshot) {
            return;
        }

        $qtyBefore = $snapshot->quantity_available;
        $qtyAfter = $qtyBefore + $qty;

        $status = InventorySnapshot::AVAILABLE;
        if ($qtyAfter <= $snapshot->low_stock_threshold) {
            $status = InventorySnapshot::LOW;
        }

        $snapshot->update([
            'quantity_available' => $qtyAfter,
            'stock_status' => $status,
        ]);

        InventoryLedger::create([
            'product_id' => $productId,
            'source' => $source,
            'quantity_before' => $qtyBefore,
            'quantity_after' => $qtyAfter,
            'quantity_delta' => $qty,
            'external_reference' => $reference,
            'occurred_at' => now(),
            'meta' => $meta,
        ]);
    }

    protected function shipmentGroupCode(int $userId, Address $address): string
    {
        $fingerprint = implode('|', [
            $userId,
            mb_strtolower(trim($address->recipient_name)),
            mb_strtolower(trim($address->address_line)),
            mb_strtolower(trim($address->district_name)),
            mb_strtolower(trim((string) $address->postal_code)),
        ]);

        return 'SG-'.substr(hash('sha256', $fingerprint), 0, 16);
    }
}
