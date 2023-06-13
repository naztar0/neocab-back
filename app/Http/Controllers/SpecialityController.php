<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Speciality;
use App\Filters\GroupFilters;

class SpecialityController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Speciality::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Speciality $speciality)
    {
        return $speciality;
    }

    public function getFacultySpecialities(Faculty $faculty, GroupFilters $filters)
    {
        $filters->apply(Speciality::class);
        return response($faculty->specialities()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
