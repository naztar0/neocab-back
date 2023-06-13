<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Subject;
use Illuminate\Http\Response;

class SubjectController extends Controller
{
    public function show(Subject $subject): Response
    {
        return response($subject->load('professor', 'department'));
    }

    public function getGroupSubjects(Group $group, int $semester): Response
    {
        $this->authorize('getGroupSubjects', $group);

        return response($group->syllabuses()->where('semester', $semester)->get()
            ->load('subject')->pluck('subject'));
    }
}
