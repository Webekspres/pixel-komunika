<?php

namespace App\Http\Controllers\Admin;

use App\Domains\Reporting\ReportingService;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, ReportingService $reporting): View
    {
        $period = $request->string('period')->toString();
        $district = trim($request->string('district')->toString());

        $tz = 'Asia/Jakarta';
        $now = Carbon::now($tz);
        [$from, $to] = match ($period) {
            '7d' => [$now->copy()->subDays(7)->startOfDay(), null],
            '30d' => [$now->copy()->subDays(30)->startOfDay(), null],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            default => [null, null],
        };

        $summary = $reporting->salesSummary($from, $to, $district !== '' ? $district : null);

        $transactions = Order::query()
            ->with(['user', 'latestPaymentProof'])
            ->whereIn('status', ['shipped', 'completed'])
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('created_at', '<=', $to))
            ->when($district !== '', fn ($q) => $q->where('shipping_district', $district))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $districts = Order::query()
            ->whereIn('status', ['shipped', 'completed'])
            ->distinct()
            ->pluck('shipping_district')
            ->filter()
            ->sort()
            ->values();

        $orderCount = $summary['order_count'];

        return view('admin.reports.index', [
            'period' => $period,
            'district' => $district,
            'districts' => $districts,
            'omzet' => $summary['omzet'],
            'pph22' => $summary['pph22'],
            'orderCount' => $orderCount,
            'avgPerOrder' => $orderCount > 0 ? $summary['omzet'] / $orderCount : 0,
            'transactions' => $transactions,
        ]);
    }
}
