<?php

namespace App\Policies;

use App\Models\FaqItem;
use App\Models\User;

class FaqItemPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     * @noinspection PhpUnusedParameterInspection
     */
    public function update(User $user, FaqItem $faqItem): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     * @noinspection PhpUnusedParameterInspection
     */
    public function delete(User $user, FaqItem $faqItem): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }
}
