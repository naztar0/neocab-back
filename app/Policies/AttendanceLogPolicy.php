<?php

namespace App\Policies;

use App\Models\AttendanceLog;
use App\Models\User;

class AttendanceLogPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AttendanceLog $attendanceLog): bool
    {
        return $user->id == $attendanceLog->user_id ||
            $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $attendanceLog->user->group_id ||
            $user->role == USER::ROLE_PROFESSOR && $user->id == $attendanceLog->scheduleSubject->subject->professor_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AttendanceLog $attendanceLog): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $attendanceLog->user->group_id ||
            $user->role == USER::ROLE_PROFESSOR && $user->id == $attendanceLog->scheduleSubject->subject->professor_id;
    }
}
