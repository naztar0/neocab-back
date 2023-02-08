<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Cathedra;
use App\Filters\GroupFilters;

class CathedraController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Cathedra::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Cathedra $cathedra)
    {
        return $cathedra;
    }

    public function getProgramCathedras(Program $program, GroupFilters $filters)
    {
        $filters->apply(Cathedra::class);
        return response($program->cathedras()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityCathedras(Speciality $speciality, GroupFilters $filters)
    {
        $filters->apply(Cathedra::class);
        return response($speciality->programs()->with('cathedras')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyCathedras(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Cathedra::class);
        return response($faculty->specialities()->with('programs.cathedras')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
