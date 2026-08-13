<?php

namespace App\Domains\PosIntegration;

use App\Models\Order;
use App\Models\PosIntegrationOperation;
use App\Models\SalesReturn;
use Illuminate\Support\Str;

class PosOutboxService
{
    public function queueSaleReport(Order $order): PosIntegrationOperation
    {
        $payload = [
            'order_number' => $order->order_number,
            'grand_total' => (float) $order->grand_total,
            'item_count' => $order->items()->count(),
        ];

        return PosIntegrationOperation::query()->firstOrCreate(
            [
                'operation' => PosIntegrationOperation::WEB_SALE_REPORT,
                'external_reference' => 'sale:'.$order->order_number,
            ],
            [
                'order_id' => $order->id,
                'request_hash' => hash('sha256', json_encode($payload)),
                'status' => PosIntegrationOperation::PENDING,
                'attempt_count' => 0,
                'correlation_id' => (string) Str::uuid(),
                'request_payload_redacted' => $payload,
            ],
        );
    }

    public function queueReturnReport(Order $order, SalesReturn $salesReturn): PosIntegrationOperation
    {
        $payload = [
            'order_number' => $order->order_number,
            'return_number' => $salesReturn->return_number,
            'reason' => $salesReturn->reason,
        ];

        return PosIntegrationOperation::query()->firstOrCreate(
            [
                'operation' => PosIntegrationOperation::WEB_RETURN_REPORT,
                'external_reference' => 'return:'.$salesReturn->return_number,
            ],
            [
                'order_id' => $order->id,
                'request_hash' => hash('sha256', json_encode($payload)),
                'status' => PosIntegrationOperation::PENDING,
                'attempt_count' => 0,
                'correlation_id' => (string) Str::uuid(),
                'request_payload_redacted' => $payload,
            ],
        );
    }
}
