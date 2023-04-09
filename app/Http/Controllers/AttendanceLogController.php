<?php

namespace App\Http\Controllers;

use App\Filters\AttendanceFilters;
use App\Models\AttendanceLog;
use App\Models\Group;
use App\Models\ScheduleSubject;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Response;

class AttendanceLogController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function show(AttendanceLog $attendanceLog): Response
    {
        return response($attendanceLog->load('user', 'scheduleSubject.subject'));
    }

    public function destroy(AttendanceLog $attendanceLog): Response
    {
        return response($attendanceLog->delete());
    }

    public function getUserAttendanceLogs(User $user): Response
    {
        return response($this->service->getUserAttendanceLogs($user));
    }

    public function getGroupAttendanceLogs(Group $group, AttendanceFilters $filters): Response
    {
        $filters->apply(AttendanceLog::class);
        return response($this->service->getGroupAttendanceLogs($group, $filters));
    }

    public function getScheduleSubjectAttendanceLogs(ScheduleSubject $scheduleSubject): Response
    {
        return response($scheduleSubject->attendanceLogs()->with('user')
            ->where('date', '>=', now()->subMonth())->get());
    }

    public function switchAttendanceLog(User $user, ScheduleSubject $scheduleSubject): Response
    {
        if ($scheduleSubject->attendanceLogs()
            ->where('user_id', $user->id)
            ->where('date', request('date'))
            ->exists()
        ) {
            return response($scheduleSubject->attendanceLogs()->where('user_id', $user->id)
                ->where('date', request('date'))->delete());
        }

        return response($scheduleSubject->attendanceLogs()->create([
            'user_id' => $user->id,
            'schedule_subject_id' => $scheduleSubject->id,
            'date' => request('date'),
        ]));
    }
}
