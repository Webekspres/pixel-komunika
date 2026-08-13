<?php

namespace App\Policies;

use App\Models\CustomerProfile;
use App\Models\User;

class CustomerProfilePolicy
{
    public function review(User $user, CustomerProfile $profile): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, CustomerProfile $profile): bool
    {
        return $user->isAdmin() || $profile->user_id === $user->id;
    }
}
