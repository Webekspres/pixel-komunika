<?php

namespace App\Console\Commands;

use App\Domains\BackgroundJobs\WorkdayCalculator;
use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Console\Command;

class AutoCompleteShippedOrders extends Command
{
    protected $signature = 'orders:auto-complete-shipped';

    protected $description = 'Auto-complete shipped orders after N workdays unless TERKENDALA';

    public function handle(FulfillmentService $fulfillment, WorkdayCalculator $workdays): int
    {
        $required = (int) config('store.fulfillment.auto_complete_workdays', 5);

        $orders = Order::query()
            ->with('shipment')
            ->where('status', 'shipped')
            ->whereHas('shipment', fn ($q) => $q
                ->where('issue_status', Shipment::ISSUE_NONE)
                ->whereNotNull('shipped_at'))
            ->get();

        $completed = 0;

        foreach ($orders as $order) {
            $shippedAt = $order->shipment?->shipped_at;
            if (! $shippedAt) {
                continue;
            }

            if ($workdays->workdaysSince($shippedAt) < $required) {
                continue;
            }

            try {
                $fulfillment->transition($order, 'completed');
                $completed++;
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        $this->info("Auto-completed {$completed} shipped order(s).");

        return self::SUCCESS;
    }
}
