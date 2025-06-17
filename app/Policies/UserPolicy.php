<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function promote(User $currentUser, User $targetUser): bool
    {
        return $currentUser->role === 'admin' && $targetUser->role !== 'admin';
    }
}
