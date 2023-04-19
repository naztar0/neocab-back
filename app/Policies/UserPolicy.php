<?php

namespace App\Policies;

use App\Models\User;
use App\Models\scheduleSubject;

class UserPolicy
{
    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $user->id == $model->id ||
            $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $model->group_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $user->id == $model->id ||
            $user->role == USER::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }

    public function getUserNotifications(User $user, User $model): bool
    {
        return $user->id == $model->id ||
            $user->role == User::ROLE_ADMIN;
    }

    public function createUserNotification(User $user): bool
    {
        return $user->role == User::ROLE_ADMIN;
    }

    public function updateAttendance(User $user, User $model, scheduleSubject $scheduleSubject): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $model->group_id ||
            $user->role == USER::ROLE_PROFESSOR && $user->id == $scheduleSubject->subject->professor_id;
    }

    public function getAzureSchedule(User $user, User $model): bool
    {
        return $user->id == $model->id ||
            $user->role == User::ROLE_ADMIN;
    }
}
