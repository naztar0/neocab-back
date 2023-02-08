<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Filters\GroupFilters;

class ProgramController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Program::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Program $program)
    {
        return $program;
    }

    public function getSpecialityPrograms(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Program::class);
        return response($speciality->programs()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyPrograms(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Program::class);
        return response($faculty->specialities()->with('programs')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
