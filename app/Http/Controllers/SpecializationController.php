<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Cathedra;
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

    public function getCathedraSpecializations(Cathedra $cathedra, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($cathedra->specializations()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramSpecializations(Program $program, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($program->cathedras()->with('specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialitySpecializations(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($speciality->programs()->with('cathedras.specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultySpecializations(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Specialization::class);
        return response($faculty->specialities()->with('programs.cathedras.specializations')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
