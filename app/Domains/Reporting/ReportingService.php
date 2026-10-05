<?php

namespace App\Domains\Reporting;

use App\Models\Order;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    /**
     * Omzet counted at SHIPPED (status=shipped), not double-counted at completed.
     *
     * @return array{omzet: float, pph22: float, order_count: int, by_district: Collection}
     */
    public function salesSummary(?CarbonInterface $from = null, ?CarbonInterface $to = null, ?string $district = null): array
    {
        $query = $this->shippedOrders($from, $to, $district);

        // Use subtotal+shipping as omzet base from shipped moment; completed shares same snapshot.
        $omzet = (float) (clone $query)->sum(DB::raw('subtotal + shipping_cost'));
        $pph22 = (float) (clone $query)->sum('tax_pph22');
        $count = (clone $query)->count();

        $byDistrict = (clone $query)
            ->select('shipping_district', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(subtotal + shipping_cost) as omzet'))
            ->groupBy('shipping_district')
            ->get();

        return [
            'omzet' => $omzet,
            'pph22' => $pph22,
            'order_count' => $count,
            'by_district' => $byDistrict,
        ];
    }

    /**
     * FR-RPT-001: periode omzet mengikuti tanggal pesanan dikirim (SHIPPED), bukan tanggal dibuat.
     *
     * @return Builder<Order>
     */
    public function shippedOrders(?CarbonInterface $from = null, ?CarbonInterface $to = null, ?string $district = null): Builder
    {
        return Order::query()
            ->whereIn('status', ['shipped', 'completed'])
            ->when($from || $to, fn ($q) => $q->whereHas('shipment', fn ($s) => $s
                ->when($from, fn ($s) => $s->where('shipped_at', '>=', $from))
                ->when($to, fn ($s) => $s->where('shipped_at', '<=', $to))))
            ->when($district, fn ($q) => $q->where('shipping_district', $district));
    }
}
