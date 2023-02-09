<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Department;
use App\Filters\GroupFilters;

class DepartmentController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Department::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Department $department)
    {
        return $department;
    }

    public function getProgramDepartments(Program $program, GroupFilters $filters)
    {
        $filters->apply(Department::class);
        return response($program->departments()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityDepartments(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Department::class);
        return response($speciality->programs()->with('departments')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyDepartments(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Department::class);
        return response($faculty->specialities()->with('programs.departments')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
