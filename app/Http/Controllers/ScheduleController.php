<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Schedule;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Response;

class ScheduleController extends Controller
{
    public function show(Schedule $schedule): Response
    {
        return response($schedule->load('scheduleSubjects.subject'));
    }

    public function update(Schedule $schedule): Response
    {
        return response(tap($schedule)->update(request()->all()));
    }

    public function destroy(Schedule $schedule): Response
    {
        return response($schedule->delete());
    }

    public function getGroupSchedules(Group $group): Response
    {
        return response($group->schedules()->with('scheduleSubjects.subject')->get());
    }

    public function createGroupSchedule(Group $group): Response
    {
        return response($group->schedules()->create(request()->all()));
    }

    public function getAzureSchedule(User $user, UserService $service): Response
    {
        return response($service->getAzureSchedule($user));
    }
}
