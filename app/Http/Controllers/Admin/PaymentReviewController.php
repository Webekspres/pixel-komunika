<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentReviewController extends Controller
{
    /**
     * Stream the proof file from the private local disk via a temporary signed
     * URL. The file remains on the private disk and is never exposed publicly.
     */
    public function show(PaymentProof $paymentProof): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($paymentProof->proof_path), 404);

        return Storage::disk('local')->response($paymentProof->proof_path);
    }
}
