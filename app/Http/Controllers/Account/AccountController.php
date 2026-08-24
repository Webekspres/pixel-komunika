<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    private function loadUser(Request $request)
    {
        return $request->user()->load(['customerProfile', 'addresses']);
    }

    private function redirectAdmin(Request $request): ?RedirectResponse
    {
        return $request->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : null;
    }

    public function show(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectAdmin($request)) {
            return $redirect;
        }

        $user = $this->loadUser($request);

        $waitingPaymentCount = Order::where('user_id', $user->id)
            ->whereIn('status', ['unpaid', 'payment_pending', 'payment_rejected'])
            ->count();

        $processingCount = Order::where('user_id', $user->id)
            ->whereIn('status', ['paid', 'processing', 'packed'])
            ->count();

        $shippedCount = Order::where('user_id', $user->id)
            ->where('status', 'shipped')
            ->count();

        $recentOrders = Order::where('user_id', $user->id)
            ->with(['items.product', 'shipment'])
            ->latest()
            ->take(3)
            ->get();

        $primaryAddress = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();

        return view('account.dashboard', [
            'user' => $user,
            'waitingPaymentCount' => $waitingPaymentCount,
            'processingCount' => $processingCount,
            'shippedCount' => $shippedCount,
            'recentOrders' => $recentOrders,
            'primaryAddress' => $primaryAddress,
        ]);
    }

    public function profile(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectAdmin($request)) {
            return $redirect;
        }

        $user = $this->loadUser($request);

        return view('account.profile', [
            'user' => $user,
        ]);
    }

    public function addresses(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectAdmin($request)) {
            return $redirect;
        }

        $user = $this->loadUser($request);

        return view('account.addresses', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:32', 'unique:users,phone,'.$request->user()->id],
            'business_name' => ['nullable', 'string', 'max:191'],
        ]);

        $request->user()->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $request->user()->customerProfile()?->update([
            'business_name' => $validated['business_name'] ?: null,
        ]);

        return back();
    }
}
