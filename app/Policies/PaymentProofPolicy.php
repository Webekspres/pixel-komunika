<?php

namespace App\Policies;

use App\Models\PaymentProof;
use App\Models\User;

class PaymentProofPolicy
{
    public function view(User $user, PaymentProof $proof): bool
    {
        return $user->isAdmin() || $proof->user_id === $user->id;
    }

    public function review(User $user, PaymentProof $proof): bool
    {
        return $user->isAdmin();
    }
}
