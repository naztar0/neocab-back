<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Department;
use App\Models\Specialization;
use App\Filters\GroupFilters;

class SpecializationController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Specialization::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Specialization $specialization)
    {
        return $specialization;
    }

    public function getDepartmentSpecializations(Department $department, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($department->specializations()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramSpecializations(Program $program, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($program->departments()->with('specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialitySpecializations(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($speciality->programs()->with('departments.specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultySpecializations(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($faculty->specialities()->with('programs.departments.specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
