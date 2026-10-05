<?php

namespace App\Http\Controllers;

use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ReceiptConfirmationController extends Controller
{
    // GET hanya menampilkan konfirmasi: pratinjau link WhatsApp / pemindai tautan
    // tidak boleh menyelesaikan pesanan (FR-ORD-011/012).
    public function show(Request $request, Order $order): View
    {
        $token = (string) $request->query('token', '');
        abort_unless($this->tokenMatches($order, $token), 410, 'Tautan konfirmasi tidak valid.');

        if ($order->status === 'completed' && $order->receipt_confirmed_at) {
            return view('orders.receipt-confirmed', ['order' => $order, 'state' => 'already']);
        }

        abort_unless($order->status === 'shipped', 410, 'Pesanan tidak dapat dikonfirmasi.');

        return view('orders.receipt-confirmed', ['order' => $order, 'state' => 'pending', 'token' => $token]);
    }

    public function store(Request $request, Order $order, FulfillmentService $fulfillment): View
    {
        try {
            $order = $fulfillment->confirmReceipt($order, (string) $request->input('token', ''));
        } catch (InvalidArgumentException $e) {
            abort(410, $e->getMessage());
        }

        return view('orders.receipt-confirmed', ['order' => $order, 'state' => 'confirmed']);
    }

    protected function tokenMatches(Order $order, string $token): bool
    {
        return $token !== '' && hash_equals((string) $order->receipt_token_hash, hash('sha256', $token));
    }
}
