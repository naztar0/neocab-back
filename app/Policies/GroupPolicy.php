<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Group;

class GroupPolicy
{
    /**
     * Determine whether the user can create models.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role == USER::ROLE_ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Group $group
     * @return bool
     */
    public function update(User $user, Group $group): bool
    {
        return $user->role == USER::ROLE_ADMIN || (
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $group->id
        );
    }

    public function getGroupAttendanceLogs(User $user, Group $group): bool
    {
        return $user->role == USER::ROLE_ADMIN ||
            $user->role == USER::ROLE_HEADMAN && $user->group_id == $group->id;
    }

    public function getGroupSchedules(User $user, Group $group): bool
    {
        return $user->group_id == $group->id ||
            $user->role == User::ROLE_ADMIN;
    }

    public function createGroupSchedule(User $user, Group $group): bool
    {
        return $user->role == User::ROLE_ADMIN ||
            $user->role == User::ROLE_HEADMAN && $user->group_id == $group->id;
    }

    public function getGroupSubjects(User $user, Group $group): bool
    {
        return $user->group_id == $group->id ||
            $user->role == User::ROLE_ADMIN;
    }
}
