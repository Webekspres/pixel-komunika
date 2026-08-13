<?php

namespace App\Services\Admin;

use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use Carbon\Carbon;

class AdminDashboardService
{
    public function build(): array
    {
        $tz = 'Asia/Jakarta';
        $today = Carbon::now($tz)->toDateString();
        $yesterday = Carbon::now($tz)->subDay()->toDateString();
        $weekStart = Carbon::now($tz)->startOfWeek()->toDateString();

        $ordersToday = Order::query()->whereDate('created_at', $today)->count();
        $ordersYesterday = Order::query()->whereDate('created_at', $yesterday)->count();
        $ordersTodayDelta = $ordersToday - $ordersYesterday;

        $pendingPayments = PaymentProof::query()->where('status', 'pending')->count();

        $activeCustomers = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::ACTIVE)
            ->count();
        $activeThisWeek = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::ACTIVE)
            ->whereDate('reviewed_at', '>=', $weekStart)
            ->count();

        $totalRevenue = (float) Order::query()
            ->whereIn('status', ['shipped', 'completed'])
            ->sum('grand_total');

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $pendingVerifications = CustomerProfile::query()
            ->with('user')
            ->where('verification_status', CustomerProfile::PENDING)
            ->latest()
            ->limit(3)
            ->get();

        $pendingVerificationCount = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::PENDING)
            ->count();

        return [
            'ordersToday' => $ordersToday,
            'ordersTodayTrend' => $ordersTodayDelta === 0
                ? 'Sama dengan kemarin'
                : (($ordersTodayDelta > 0 ? '+' : '').$ordersTodayDelta.' dari kemarin'),
            'ordersTodayTrendUp' => $ordersTodayDelta >= 0,
            'pendingPayments' => $pendingPayments,
            'activeCustomers' => $activeCustomers,
            'activeCustomersTrend' => $activeThisWeek > 0
                ? '+'.$activeThisWeek.' minggu ini'
                : 'Tidak ada aktivasi minggu ini',
            'activeCustomersTrendUp' => $activeThisWeek > 0,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
            'pendingVerifications' => $pendingVerifications,
            'pendingVerificationCount' => $pendingVerificationCount,
            'productSkuCount' => Product::query()->count(),
        ];
    }

    public static function orderStatusLabel(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Menunggu Pembayaran',
            'payment_pending' => 'Pembayaran Diajukan',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            'returned' => 'Diretur',
            default => strtoupper($status),
        };
    }

    public static function orderStatusClasses(string $status): string
    {
        return match ($status) {
            'unpaid' => 'bg-amber-100 text-amber-800',
            'payment_pending' => 'bg-orange-100 text-orange-700',
            'paid' => 'bg-emerald-100 text-emerald-800',
            'processing' => 'bg-purple-100 text-purple-800',
            'shipped' => 'bg-sky-100 text-sky-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-zinc-100 text-zinc-600',
            'returned' => 'bg-red-100 text-red-700',
            default => 'bg-zinc-100 text-zinc-700',
        };
    }
}
