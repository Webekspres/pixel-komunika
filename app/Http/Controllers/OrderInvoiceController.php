<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\StoreProfile;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class OrderInvoiceController extends Controller
{
    public function show(Request $request, Order $order): View
    {
        return view('orders.invoice', $this->invoiceData($request, $order));
    }

    public function download(Request $request, Order $order): SymfonyResponse
    {
        $data = $this->invoiceData($request, $order);
        $filename = ($data['invoice']?->invoice_number ?? $order->order_number).'.pdf';

        return Pdf::loadView('orders.invoice-pdf', $data)
            ->download($filename);
    }

    /**
     * @return array{order: Order, invoice: \App\Models\Invoice|null, storeProfile: StoreProfile|null}
     */
    protected function invoiceData(Request $request, Order $order): array
    {
        $user = $request->user();

        if (! $user->isAdmin() && $order->user_id !== $user->id) {
            abort(403);
        }

        $order->load(['user.customerProfile', 'items.product', 'invoice', 'shipment']);

        return [
            'order' => $order,
            'invoice' => $order->invoice,
            'storeProfile' => StoreProfile::active(),
        ];
    }
}
