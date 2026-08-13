<?php

namespace App\Domains\CustomerManagement;

use App\Domains\Audit\AuditLogger;
use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CustomerVerificationService
{
    public function __construct(
        protected AuditLogger $audit,
    ) {}

    public function transition(CustomerProfile $profile, string $action, User $admin, ?string $reason = null): CustomerProfile
    {
        [$status, $resolvedReason] = match ($action) {
            'approve', 'reactivate' => [CustomerProfile::ACTIVE, null],
            'reject' => [CustomerProfile::REJECTED, $reason ?: 'Permohonan belum dapat disetujui.'],
            'suspend' => [CustomerProfile::SUSPENDED, $reason ?: 'Akun ditangguhkan sementara.'],
            default => throw new \InvalidArgumentException("Aksi {$action} tidak dikenal."),
        };

        return DB::transaction(function () use ($profile, $action, $admin, $status, $resolvedReason) {
            $old = $profile->verification_status;
            $payload = [
                'verification_status' => $status,
                'rejection_reason' => $resolvedReason,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ];

            if ($status === CustomerProfile::ACTIVE && blank($profile->reseller_account_number)) {
                $payload['reseller_account_number'] = $this->nextResellerAccountNumber($profile);
            }

            $profile->update($payload);

            $this->audit->log(
                'CUSTOMER_'.strtoupper($action),
                $profile,
                $admin,
                ['verification_status' => $old],
                ['verification_status' => $status, 'reseller_account_number' => $profile->reseller_account_number],
            );

            return $profile->fresh();
        });
    }

    protected function nextResellerAccountNumber(CustomerProfile $profile): string
    {
        // ponytail: OPN-022 format provisional
        $prefix = config('store.reseller_account_prefix', 'PKR');

        return sprintf('%s-%06d', $prefix, $profile->id);
    }
}
