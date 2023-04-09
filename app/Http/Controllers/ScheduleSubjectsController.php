<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleSubject;
use App\Models\Subject;
use Illuminate\Http\Response;

class ScheduleSubjectsController extends Controller
{
    public function show(ScheduleSubject $scheduleSubject): Response
    {
        return response($scheduleSubject);
    }

    public function update(ScheduleSubject $scheduleSubject): Response
    {
        return response(tap($scheduleSubject)->update(request()->all()));
    }

    public function destroy(ScheduleSubject $scheduleSubject): Response
    {
        return response($scheduleSubject->delete());
    }

    public function createScheduleSubject(Schedule $schedule, Subject $subject): Response
    {
        $values = array_merge(
            request()->all(),
            ['subject_id' => $subject->id],
        );

        return response($schedule->scheduleSubjects()->create($values));
    }
}
