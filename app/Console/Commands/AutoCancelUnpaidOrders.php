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

        // FR-ORD-010 + keputusan 5 Okt: order yang pembayarannya ditolak ikut batal
        // setelah hari penolakan berakhir (pelanggan masih bisa unggah ulang hari itu),
        // agar stoknya tidak tertahan selamanya.
        $orders = Order::query()
            ->where(fn ($q) => $q->where('status', 'unpaid')->where('created_at', '<', $cutoff))
            ->orWhere(fn ($q) => $q->where('status', 'payment_rejected')->where('updated_at', '<', $cutoff))
            ->get();

        $cancelled = 0;

        foreach ($orders as $order) {
            try {
                $orderService->cancelOrder(
                    $order,
                    $order->status === 'payment_rejected'
                        ? 'Bukti pembayaran ditolak dan tidak diunggah ulang sebelum batas waktu.'
                        : 'Pembayaran tidak diterima sebelum batas waktu (auto-cancel D+1).',
                    'SYSTEM',
                    onlyFrom: ['unpaid', 'payment_rejected'],
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
