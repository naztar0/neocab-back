<?php

namespace App\Policies;

use App\Models\ScheduleSubject;
use App\Models\User;

class ScheduleSubjectPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScheduleSubject $scheduleSubject): bool
    {
        return $user->group_id == $scheduleSubject->schedule->group_id ||
            $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_PROFESSOR && $user->id == $scheduleSubject->subject->professor_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScheduleSubject $scheduleSubject): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $scheduleSubject->schedule->group_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScheduleSubject $scheduleSubject): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $scheduleSubject->schedule->group_id;
    }

    public function getScheduleSubjectAttendanceLogs(User $user, ScheduleSubject $scheduleSubject): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $scheduleSubject->schedule->group_id ||
            $user->role == USER::ROLE_PROFESSOR && $user->id == $scheduleSubject->subject->professor_id;
    }
}
