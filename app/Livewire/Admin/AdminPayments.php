<?php

namespace App\Livewire\Admin;

use App\Models\BankAccount;
use App\Models\PaymentProof;
use App\Services\PaymentService;
use Illuminate\Support\Facades\URL as UrlFacade;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Admin - Manajemen Pembayaran')]
class AdminPayments extends Component
{
    use WithPagination;

    #[Url]
    public string $statusFilter = 'all';

    public string $search = '';

    public string $bankFilter = '';

    public bool $showReviewModal = false;

    public ?int $reviewingProofId = null;

    public string $adminNote = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingBankFilter(): void
    {
        $this->resetPage();
    }

    public function openReview(int $proofId): void
    {
        $this->reviewingProofId = $proofId;
        $this->adminNote = '';
        $this->showReviewModal = true;
    }

    public function resetReviewModal(): void
    {
        $this->reset(['reviewingProofId', 'adminNote', 'showReviewModal']);
    }

    public function approvePayment(PaymentService $paymentService): void
    {
        $proof = $this->reviewingProof();

        if (! $proof) {
            return;
        }

        $this->validate([
            'adminNote' => ['nullable', 'string', 'max:1000'],
        ]);

        $paymentService->approvePayment($proof, auth()->user());

        if ($this->adminNote !== '' && $proof->payment) {
            $proof->payment->update(['admin_note' => $this->adminNote]);
        }

        $this->resetReviewModal();
        session()->flash('success', "Pembayaran Order #{$proof->order->order_number} telah disetujui.");
    }

    public function rejectPayment(PaymentService $paymentService): void
    {
        $proof = $this->reviewingProof();

        if (! $proof) {
            return;
        }

        $this->validate([
            'adminNote' => ['required', 'string', 'min:5'],
        ]);

        $paymentService->rejectPayment($proof, $this->adminNote, auth()->user());

        $this->resetReviewModal();
        session()->flash('success', "Pembayaran Order #{$proof->order->order_number} telah ditolak.");
    }

    public function previewUrl(PaymentProof $proof): string
    {
        return UrlFacade::temporarySignedRoute(
            'admin.payments.show',
            now()->addMinutes(30),
            ['paymentProof' => $proof->id]
        );
    }

    public function isPdf(PaymentProof $proof): bool
    {
        return strtolower(pathinfo($proof->proof_path, PATHINFO_EXTENSION)) === 'pdf';
    }

    protected function reviewingProof(): ?PaymentProof
    {
        return $this->reviewingProofId
            ? PaymentProof::with(['order.user', 'payment.bankAccount', 'reviewer'])->find($this->reviewingProofId)
            : null;
    }

    protected function scopeVisible($query)
    {
        return $query->where(function ($q) {
            $q->where('is_active', true)
                ->orWhereIn('status', ['approved', 'rejected']);
        });
    }

    public function render()
    {
        $query = $this->scopeVisible(PaymentProof::query())
            ->with(['order.user', 'payment.bankAccount', 'reviewer'])
            ->latest('id');

        if ($this->statusFilter === 'pending') {
            $query->where('status', 'pending')->where('is_active', true);
        } elseif ($this->statusFilter === 'approved') {
            $query->where('status', 'approved');
        } elseif ($this->statusFilter === 'rejected') {
            $query->where('status', 'rejected');
        }

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('bank_name', 'like', "%{$this->search}%")
                    ->orWhereHas('order', function ($o) {
                        $o->where('order_number', 'like', "%{$this->search}%")
                            ->orWhere('recipient_name', 'like', "%{$this->search}%")
                            ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$this->search}%"));
                    })
                    ->orWhereHas('payment.bankAccount', fn ($b) => $b->where('bank_name', 'like', "%{$this->search}%"));
            });
        }

        if (! empty($this->bankFilter)) {
            $query->whereHas('payment', fn ($p) => $p->where('bank_account_id', $this->bankFilter));
        }

        $counts = [
            'all' => $this->scopeVisible(PaymentProof::query())->count(),
            'pending' => PaymentProof::query()->where('status', 'pending')->where('is_active', true)->count(),
            'approved' => PaymentProof::query()->where('status', 'approved')->count(),
            'rejected' => PaymentProof::query()->where('status', 'rejected')->count(),
        ];

        return view('livewire.admin.admin-payments', [
            'proofs' => $query->paginate(15),
            'banks' => BankAccount::query()->orderBy('bank_name')->get(),
            'counts' => $counts,
            'reviewingProof' => $this->reviewingProof(),
        ])->layout('components.layouts.app');
    }
}
