<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\Group;
use App\Filters\GroupFilters;

class GroupController extends Controller
{
    public function show(Group $group): Response
    {
        return response($group);
    }

    public function index(GroupFilters $filters): Response
    {
        return response($filters->apply(Group::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function getSpecializationGroups(Specialization $specialization, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($specialization->groups()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getDepartmentGroups(Department $department, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($department->specializations()->with('groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramGroups(Program $program, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($program->departments()->with('specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityGroups(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($speciality->programs()->with('departments.specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyGroups(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Group::class);
        return response($faculty->specialities()->with('programs.departments.specializations.groups')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
