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
        $this->authorize('view', $attendanceLog);
        return response($attendanceLog->load('user', 'scheduleSubject.subject'));
    }

    public function destroy(AttendanceLog $attendanceLog): Response
    {
        $this->authorize('delete', $attendanceLog);
        return response($attendanceLog->delete());
    }

    public function getUserAttendanceLogs(User $user): Response
    {
        $this->authorize('view', $user);
        return response($this->service->getUserAttendanceLogs($user));
    }

    public function getGroupAttendanceLogs(Group $group, AttendanceFilters $filters): Response
    {
        $this->authorize('getGroupAttendanceLogs', $group);
        $filters->apply(AttendanceLog::class);

        return response($this->service->getGroupAttendanceLogs($group, $filters));
    }

    public function getScheduleSubjectAttendanceLogs(ScheduleSubject $scheduleSubject): Response
    {
        $this->authorize('getScheduleSubjectAttendanceLogs', $scheduleSubject);

        return response($scheduleSubject->attendanceLogs()->with('user')
            ->where('date', '>=', now()->subMonth())->get());
    }

    public function switchAttendanceLog(User $user, ScheduleSubject $scheduleSubject): Response
    {
        $this->authorize('updateAttendance', [$user, $scheduleSubject]);

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
