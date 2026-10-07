<?php

namespace App\Services\Admin;

use App\Domains\Reporting\ReportingService;
use App\Models\CustomerProfile;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Models\Product;
use App\Models\SyncRun;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    public function __construct(
        protected ReportingService $reporting,
    ) {}

    public function build(): array
    {
        $tz = 'Asia/Jakarta';
        $now = Carbon::now($tz);
        $today = $now->toDateString();
        $yesterday = $now->copy()->subDay()->toDateString();
        $weekStart = $now->copy()->startOfWeek()->toDateString();

        $ordersToday = Order::query()->whereDate('created_at', $today)->count();
        $ordersYesterday = Order::query()->whereDate('created_at', $yesterday)->count();
        $ordersTodayDelta = $ordersToday - $ordersYesterday;

        $pendingProcessOrdersCount = Order::query()
            ->whereIn('status', ['paid', 'processing', 'payment_pending'])
            ->count();

        $pendingPayments = PaymentProof::query()->where('status', 'pending')->count();
        $pendingPaymentProofs = PaymentProof::query()
            ->with(['order', 'user.customerProfile'])
            ->where('status', 'pending')
            ->latest()
            ->limit(4)
            ->get();

        $activeCustomers = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::ACTIVE)
            ->count();
        $activeThisWeek = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::ACTIVE)
            ->whereDate('reviewed_at', '>=', $weekStart)
            ->count();

        // Omzet = definisi laporan (subtotal + ongkir, status shipped/completed) agar angka sama.
        $totalRevenue = $this->reporting->salesSummary()['omzet'];
        $revenueThisMonth = $this->reporting->salesSummary($now->copy()->startOfMonth(), $now)['omzet'];
        $lastMonth = $now->copy()->subMonthNoOverflow();
        $revenueLastMonth = $this->reporting->salesSummary($lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth())['omzet'];

        $revenueGrowthPercent = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : null;

        $recentOrders = Order::query()
            ->with(['user.customerProfile', 'items.product'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingVerifications = CustomerProfile::query()
            ->with('user')
            ->where('verification_status', CustomerProfile::PENDING)
            ->latest()
            ->limit(4)
            ->get();

        $pendingVerificationCount = CustomerProfile::query()
            ->where('verification_status', CustomerProfile::PENDING)
            ->count();

        $productSkuCount = Product::query()->count();

        $lastSync = SyncRun::query()->latest('started_at')->first();
        $lastSyncTimeFormatted = $lastSync?->started_at
            ? Carbon::parse($lastSync->started_at)->timezone($tz)->diffForHumans()
            : 'Belum pernah';

        $chartDays = [];
        $chartDates = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $dayStr = $day->toDateString();
            $chartDays[] = $day->translatedFormat('D');
            $chartDates[] = $day->translatedFormat('d M');

            $dayRev = $this->reporting->salesSummary($day->copy()->startOfDay(), $day->copy()->endOfDay())['omzet'];

            $dayOrders = Order::query()
                ->whereDate('created_at', $dayStr)
                ->count();

            $chartRevenue[] = $dayRev;
            $chartOrders[] = $dayOrders;
        }

        // Top Selling SKUs
        $topSellingItems = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.status', ['shipped', 'completed'])
            ->select(
                'order_items.product_id',
                'order_items.product_name',
                'order_items.sku',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.product_id', 'order_items.product_name', 'order_items.sku')
            ->orderByDesc('total_sold')
            ->limit(3)
            ->get();

        $topSellingProducts = [];
        if ($topSellingItems->isNotEmpty()) {
            $productIds = $topSellingItems->pluck('product_id')->filter()->toArray();
            $products = Product::with('media.library')->whereIn('id', $productIds)->get()->keyBy('id');
            foreach ($topSellingItems as $item) {
                $prod = $products->get($item->product_id);
                $mediaUrl = $prod?->media?->first()?->url();
                $topSellingProducts[] = [
                    'name' => $item->product_name ?: ($prod?->displayName() ?? 'Produk'),
                    'sku' => $item->sku ?: ($prod?->sku ?? '-'),
                    'sold' => (int) $item->total_sold,
                    'revenue' => (float) $item->total_revenue,
                    'image_url' => $mediaUrl,
                ];
            }
        }

        return [
            'ordersToday' => $ordersToday,
            'ordersTodayTrend' => $ordersTodayDelta === 0
                ? 'Sama dengan kemarin'
                : (($ordersTodayDelta > 0 ? '+' : '').$ordersTodayDelta.' dari kemarin'),
            'ordersTodayTrendUp' => $ordersTodayDelta >= 0,
            'pendingProcessOrdersCount' => $pendingProcessOrdersCount,
            'pendingPayments' => $pendingPayments,
            'pendingPaymentProofs' => $pendingPaymentProofs,
            'activeCustomers' => $activeCustomers,
            'activeCustomersTrend' => $activeThisWeek > 0
                ? '+'.$activeThisWeek.' minggu ini'
                : 'Belum ada aktivasi minggu ini',
            'activeCustomersTrendUp' => $activeThisWeek > 0,
            'totalRevenue' => $totalRevenue,
            'revenueGrowthPercent' => $revenueGrowthPercent,
            'recentOrders' => $recentOrders,
            'pendingVerifications' => $pendingVerifications,
            'pendingVerificationCount' => $pendingVerificationCount,
            'productSkuCount' => $productSkuCount,
            'lastSyncTimeFormatted' => $lastSyncTimeFormatted,
            'lastSyncStatus' => $lastSync?->status,
            'posDriver' => config('pos.driver'),
            'chartDays' => $chartDays,
            'chartDates' => $chartDates,
            'chartRevenue' => $chartRevenue,
            'chartOrders' => $chartOrders,
            'topSellingProducts' => $topSellingProducts,
        ];
    }

    public static function orderStatusLabel(string $status): string
    {
        return match ($status) {
            'unpaid' => 'Menunggu Pembayaran',
            'payment_pending' => 'Pembayaran Diajukan',
            'paid' => 'Dibayar',
            'processing' => 'Diproses',
            'payment_rejected' => 'Pembayaran Ditolak',
            'packed' => 'Dikemas',
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
            'unpaid' => 'bg-amber-100 text-amber-800 border border-amber-200/60',
            'payment_pending' => 'bg-orange-100 text-orange-700 border border-orange-200/60',
            'paid' => 'bg-emerald-100 text-emerald-800 border border-emerald-200/60',
            'processing' => 'bg-purple-100 text-purple-800 border border-purple-200/60',
            'payment_rejected' => 'bg-rose-100 text-rose-700 border border-rose-200/60',
            'packed' => 'bg-indigo-100 text-indigo-800 border border-indigo-200/60',
            'shipped' => 'bg-sky-100 text-sky-800 border border-sky-200/60',
            'completed' => 'bg-green-100 text-green-800 border border-green-200/60',
            'cancelled' => 'bg-zinc-100 text-zinc-600 border border-zinc-200/60',
            'returned' => 'bg-red-100 text-red-700 border border-red-200/60',
            default => 'bg-zinc-100 text-zinc-700 border border-zinc-200/60',
        };
    }
}
