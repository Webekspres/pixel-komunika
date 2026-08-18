<?php

namespace App\Livewire\Admin;

use App\Domains\CustomerManagement\CustomerVerificationService;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail Pelanggan - Pixel Komunika')]
class CustomerReviewDetail extends Component
{
    public CustomerProfile $customer;

    public string $rejectionReason = '';

    public function mount(CustomerProfile $customerProfile): void
    {
        $this->customer = $this->loadProfile($customerProfile);
        $this->rejectionReason = $customerProfile->rejection_reason ?? '';
    }

    public function approve(): void
    {
        $this->changeStatus('approve');
    }

    public function reactivate(): void
    {
        $this->changeStatus('reactivate');
    }

    public function reject(): void
    {
        $this->validateReason();
        $this->changeStatus('reject', $this->rejectionReason);
        $this->dispatch('modal-close', name: 'reject-modal');
    }

    public function suspend(): void
    {
        $this->validateReason();
        $this->changeStatus('suspend', $this->rejectionReason);
        $this->dispatch('modal-close', name: 'suspend-modal');
    }

    protected function validateReason(): void
    {
        $this->validate([
            'rejectionReason' => ['required', 'string', 'min:5'],
        ]);
    }

    protected function changeStatus(string $action, ?string $reason = null): void
    {
        try {
            app(CustomerVerificationService::class)->transition(
                $this->customer,
                $action,
                Auth::user(),
                $reason,
            );
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->customer = $this->loadProfile($this->customer->fresh());
        $this->rejectionReason = $this->customer->rejection_reason ?? '';

        $messages = [
            'approve' => 'Pendaftaran disetujui.',
            'reactivate' => 'Akun diaktifkan kembali.',
            'reject' => 'Pendaftaran ditolak.',
            'suspend' => 'Akun ditangguhkan.',
        ];

        session()->flash('success', $messages[$action].' ('.$this->customer->user->name.').');
    }

    protected function loadProfile(CustomerProfile $profile): CustomerProfile
    {
        return $profile->load([
            'user.addresses' => fn ($query) => $query->orderByDesc('is_default')->orderBy('created_at'),
            'reviewer',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.customer-review-detail')
            ->layout('components.layouts.app');
    }
}
