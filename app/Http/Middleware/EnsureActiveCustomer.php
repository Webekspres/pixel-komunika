<?php

namespace App\Http\Middleware;

use App\Models\CustomerProfile;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->isActiveCustomer()) {
            return $next($request);
        }

        $message = $user?->customerStatus() === CustomerProfile::PENDING
            ? 'Akun masih menunggu verifikasi admin sebelum dapat checkout atau melihat riwayat pesanan.'
            : 'Checkout dan riwayat pesanan hanya tersedia untuk pelanggan terverifikasi.';

        return redirect()
            ->route('cart.index')
            ->with('error', $message);
    }
}
