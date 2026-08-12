<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class AutoCancelUnpaidOrders extends Command
{
    protected $signature = 'orders:auto-cancel-unpaid';

    protected $description = 'Cancel unpaid orders from previous calendar days and restore stock';

    public function handle(OrderService $orderService): int
    {
        $cutoff = now()->startOfDay();

        $orders = Order::query()
            ->where('status', 'unpaid')
            ->where('created_at', '<', $cutoff)
            ->get();

        $cancelled = 0;

        foreach ($orders as $order) {
            try {
                $orderService->cancelOrder(
                    $order,
                    'Pembayaran tidak diterima sebelum batas waktu (auto-cancel D+1).',
                    'SYSTEM',
                );
                $cancelled++;
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        $this->info("Auto-cancelled {$cancelled} unpaid order(s).");

        return self::SUCCESS;
    }
}
