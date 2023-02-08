<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Cathedra;
use App\Models\Specialization;
use App\Models\Group;
use App\Filters\GroupFilters;

class GroupController extends Controller
{
    public function index(GroupFilters $filters): Response
    {
        return response($filters->apply(Group::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function getSchedules(Group $group): Response
    {
        return response($group->schedules()->get());
    }

    public function getSpecializationGroups(Specialization $specialization, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($specialization->groups()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getCathedraGroups(Cathedra $cathedra, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($cathedra->specializations()->with('groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramGroups(Program $program, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($program->cathedras()->with('specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityGroups(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($speciality->programs()->with('cathedras.specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyGroups(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($faculty->specialities()->with('programs.cathedras.specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
