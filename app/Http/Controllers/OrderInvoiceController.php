<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreProfile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderInvoiceController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        $user = $request->user();

        if (! $user->isAdmin() && $order->user_id !== $user->id) {
            abort(403);
        }

        $order->load(['user.customerProfile', 'items.product', 'invoice', 'shipment']);
        $storeProfile = StoreProfile::active();

        return view('orders.invoice', [
            'order' => $order,
            'invoice' => $order->invoice,
            'storeProfile' => $storeProfile,
        ]);
    }
}
