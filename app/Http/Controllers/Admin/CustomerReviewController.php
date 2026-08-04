<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = trim($request->string('q')->toString());

        $customers = CustomerProfile::query()
            ->with(['user', 'reviewer'])
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
            ->orderBy('verification_status')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
            'selectedStatus' => $status,
            'search' => $search,
            'statuses' => [
                CustomerProfile::PENDING,
                CustomerProfile::ACTIVE,
                CustomerProfile::REJECTED,
                CustomerProfile::SUSPENDED,
            ],
        ]);
    }

    public function show(CustomerProfile $customerProfile): View
    {
        return view('admin.customers.show', [
            'customer' => $customerProfile->load(['user', 'reviewer', 'user.addresses']),
        ]);
    }

    public function update(Request $request, CustomerProfile $customerProfile): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject,suspend,reactivate'],
            'reason' => ['nullable', 'string'],
        ]);

        [$status, $reason] = match ($validated['action']) {
            'approve', 'reactivate' => [CustomerProfile::ACTIVE, null],
            'reject' => [CustomerProfile::REJECTED, $validated['reason'] ?: 'Permohonan belum dapat disetujui.'],
            'suspend' => [CustomerProfile::SUSPENDED, $validated['reason'] ?: 'Akun ditangguhkan sementara.'],
        };

        $customerProfile->update([
            'verification_status' => $status,
            'rejection_reason' => $reason,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back();
    }
}
