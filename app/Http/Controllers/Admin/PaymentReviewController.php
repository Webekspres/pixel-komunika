<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentReviewController extends Controller
{
    public function index(): View
    {
        $pending = PaymentProof::query()
            ->with(['order', 'user'])
            ->where('status', 'pending')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get();

        $history = PaymentProof::query()
            ->with(['order', 'user', 'reviewer'])
            ->whereIn('status', ['approved', 'rejected'])
            ->orderByDesc('reviewed_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', [
            'pending' => $pending,
            'history' => $history,
        ]);
    }

    /**
     * Stream the proof file from the private local disk (admin-only route).
     */
    public function show(PaymentProof $paymentProof): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($paymentProof->proof_path), 404);

        return Storage::disk('local')->response($paymentProof->proof_path);
    }

    public function update(Request $request, PaymentProof $paymentProof, PaymentService $paymentService): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'required_if:action,reject', 'string', 'min:3'],
        ]);

        if ($validated['action'] === 'approve') {
            $paymentService->approvePayment($paymentProof, $request->user());
            $message = "Pembayaran untuk Order #{$paymentProof->order->order_number} telah disetujui.";
        } else {
            $paymentService->rejectPayment($paymentProof, $validated['reason'], $request->user());
            $message = "Pembayaran untuk Order #{$paymentProof->order->order_number} telah ditolak.";
        }

        return back()->with('status', $message);
    }
}
