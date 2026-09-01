<?php

namespace App\Domains\Order;

use App\Domains\Audit\AuditLogger;
use App\Domains\Notifications\NotificationService;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FulfillmentService
{
    public function __construct(
        protected AuditLogger $audit,
        protected NotificationService $notifications,
    ) {}

    public function transition(Order $order, string $status, ?User $actor = null, ?string $trackingNumber = null): Order
    {
        $allowed = [
            'paid' => ['processing', 'cancelled'],
            'processing' => ['packed', 'cancelled'],
            'packed' => ['shipped', 'cancelled'],
            'shipped' => ['completed'],
        ];

        $from = $order->status;
        if (! in_array($status, $allowed[$from] ?? [], true)) {
            throw new InvalidArgumentException("Transisi status dari {$from} ke {$status} tidak diizinkan.");
        }

        $receiptToken = null;

        $result = DB::transaction(function () use ($order, $status, $actor, $trackingNumber, $from, &$receiptToken) {
            $payload = ['status' => $status];

            if ($status === 'shipped') {
                [$shippedPayload, $receiptToken] = $this->markShipped($order, $trackingNumber);
                $payload = array_merge($payload, $shippedPayload);
            }

            if ($status === 'completed') {
                $payload['completion_source'] = $actor ? 'ADMIN' : 'SYSTEM_5_WORKDAYS';
                $payload['receipt_confirmed_at'] = $payload['receipt_confirmed_at'] ?? now();
                $order->shipment?->update([
                    'status' => 'COMPLETED',
                    'delivered_at' => now(),
                ]);
            }

            if (in_array($status, ['processing', 'packed'], true)) {
                $order->shipment?->update(['status' => strtoupper($status)]);
            }

            $order->update($payload);

            $this->audit->log('ORDER_STATUS_CHANGED', $order, $actor, ['status' => $from], ['status' => $status]);

            return $order->fresh(['shipment']);
        });

        if ($receiptToken) {
            $this->notifications->notifyReceiptConfirmation($result, $receiptToken);
        }

        return $result;
    }

    public function markTerkendala(Order $order, string $reason, User $admin): Shipment
    {
        $shipment = $order->shipment;
        if (! $shipment) {
            throw new InvalidArgumentException('Order belum memiliki shipment.');
        }

        $shipment->update([
            'issue_status' => Shipment::ISSUE_TERKENDALA,
            'issue_reason' => $reason,
            'issue_reported_at' => now(),
            'issue_resolved_at' => null,
        ]);

        $this->audit->log('SHIPMENT_TERKENDALA', $shipment, $admin, null, ['reason' => $reason]);

        return $shipment->fresh();
    }

    public function resolveTerkendala(Order $order, User $admin): Shipment
    {
        $shipment = $order->shipment;
        if (! $shipment) {
            throw new InvalidArgumentException('Order belum memiliki shipment.');
        }

        $shipment->update([
            'issue_status' => Shipment::ISSUE_NONE,
            'issue_resolved_at' => now(),
        ]);

        $this->audit->log('SHIPMENT_ISSUE_RESOLVED', $shipment, $admin);

        return $shipment->fresh();
    }

    public function confirmReceipt(Order $order, string $token, string $source = 'CUSTOMER_WHATSAPP'): Order
    {
        if ($order->status !== 'shipped') {
            throw new InvalidArgumentException('Hanya order terkirim yang dapat dikonfirmasi.');
        }

        if ($order->receipt_confirmed_at) {
            return $order; // idempotent
        }

        $hash = hash('sha256', $token);
        if (! hash_equals((string) $order->receipt_token_hash, $hash)) {
            throw new InvalidArgumentException('Token konfirmasi tidak valid.');
        }

        if ($order->receipt_token_expires_at && $order->receipt_token_expires_at->isPast()) {
            throw new InvalidArgumentException('Token konfirmasi kedaluwarsa.');
        }

        $order->update([
            'status' => 'completed',
            'completion_source' => $source,
            'receipt_confirmed_at' => now(),
        ]);

        $order->shipment?->update([
            'status' => 'COMPLETED',
            'delivered_at' => now(),
        ]);

        $this->audit->log('ORDER_RECEIPT_CONFIRMED', $order, null, null, ['source' => $source]);

        return $order->fresh();
    }

    public function issueReceiptToken(Order $order): string
    {
        $token = Str::random(40);
        $order->update([
            'receipt_token_hash' => hash('sha256', $token),
            'receipt_token_expires_at' => now()->addDays(14),
        ]);

        return $token;
    }

    /**
     * @return array{0: array<string, mixed>, 1: ?string}
     */
    protected function markShipped(Order $order, ?string $trackingNumber): array
    {
        $order->shipment?->update([
            'status' => 'SHIPPED',
            'tracking_number' => $trackingNumber ?? $order->shipment->tracking_number,
            'shipped_at' => now(),
        ]);

        $token = null;
        if (! $order->receipt_token_hash) {
            $token = $this->issueReceiptToken($order);
        }

        return [[], $token];
    }
}
