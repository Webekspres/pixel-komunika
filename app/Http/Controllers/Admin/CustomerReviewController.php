<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $statusCounts = [
            'all' => CustomerProfile::query()->count(),
            CustomerProfile::PENDING => CustomerProfile::query()->where('verification_status', CustomerProfile::PENDING)->count(),
            CustomerProfile::ACTIVE => CustomerProfile::query()->where('verification_status', CustomerProfile::ACTIVE)->count(),
            CustomerProfile::REJECTED => CustomerProfile::query()->where('verification_status', CustomerProfile::REJECTED)->count(),
            CustomerProfile::SUSPENDED => CustomerProfile::query()->where('verification_status', CustomerProfile::SUSPENDED)->count(),
        ];

        $customers = CustomerProfile::query()
            ->with(['user.addresses' => fn ($q) => $q->orderByDesc('is_default')])
            ->when($status !== '', fn ($query) => $query->where('verification_status', $status))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search): void {
                    $query->where('business_name', 'like', '%'.$search.'%')
                        ->orWhereHas('user', function ($query) use ($search): void {
                            $query->where('name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%')
                                ->orWhere('phone', 'like', '%'.$search.'%');
                        });
                });
            })
            ->orderByRaw("CASE verification_status
                WHEN 'PENDING_VERIFICATION' THEN 0
                WHEN 'ACTIVE' THEN 1
                WHEN 'REJECTED' THEN 2
                WHEN 'SUSPENDED' THEN 3
                ELSE 4 END")
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'selectedStatus' => $status,
            'search' => $search,
            'statusCounts' => $statusCounts,
            'tabs' => [
                ['key' => '', 'label' => 'Semua', 'countKey' => 'all'],
                ['key' => CustomerProfile::PENDING, 'label' => 'Menunggu', 'countKey' => CustomerProfile::PENDING],
                ['key' => CustomerProfile::ACTIVE, 'label' => 'Aktif', 'countKey' => CustomerProfile::ACTIVE],
                ['key' => CustomerProfile::REJECTED, 'label' => 'Ditolak', 'countKey' => CustomerProfile::REJECTED],
                ['key' => CustomerProfile::SUSPENDED, 'label' => 'Dibekukan', 'countKey' => CustomerProfile::SUSPENDED],
            ],
        ]);
    }
}
