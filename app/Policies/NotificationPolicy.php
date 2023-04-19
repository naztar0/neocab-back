<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class NotificationPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Notification $notification): bool
    {
        return $user->id == $notification->user_id ||
            $user->role == User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Notification $notification): bool
    {
        return $user->id == $notification->user_id ||
            $user->role == User::ROLE_ADMIN;
    }

    public function markAsReadNotification(User $user, Notification $notification): bool
    {
        return $user->id == $notification->user_id ||
            $user->role == User::ROLE_ADMIN;
    }
}
