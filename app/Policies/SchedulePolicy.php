<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\User;

class SchedulePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Schedule $schedule): bool
    {
        return $user->group_id == $schedule->group_id ||
            $user->role == User::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Schedule $schedule): bool
    {
        return $user->role == User::ROLE_ADMIN ||
            $user->role == User::ROLE_HEADMAN && $user->group_id == $schedule->group_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Schedule $schedule): bool
    {
        return $user->role == User::ROLE_ADMIN ||
            $user->role == User::ROLE_HEADMAN && $user->group_id == $schedule->group_id;
    }

    public function createScheduleSubject(User $user, Schedule $schedule, Subject $subject): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $schedule->group_id &&
            (!$subject->department_id || $user->group->specialization->department_id == $subject->department_id);
    }
}
