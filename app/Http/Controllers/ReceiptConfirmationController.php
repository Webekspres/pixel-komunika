<?php

namespace App\Http\Controllers;

use App\Domains\Order\FulfillmentService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class ReceiptConfirmationController extends Controller
{
    public function __invoke(Request $request, Order $order, FulfillmentService $fulfillment): View
    {
        $token = (string) $request->query('token', '');

        try {
            $order = $fulfillment->confirmReceipt($order, $token);

            return view('orders.receipt-confirmed', [
                'order' => $order,
                'alreadyConfirmed' => false,
            ]);
        } catch (InvalidArgumentException $e) {
            if ($order->status === 'completed' && $order->receipt_confirmed_at) {
                return view('orders.receipt-confirmed', [
                    'order' => $order,
                    'alreadyConfirmed' => true,
                ]);
            }

            abort(410, $e->getMessage());
        }
    }
}
