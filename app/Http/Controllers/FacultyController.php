<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Filters\GroupFilters;

class FacultyController extends Controller
{
    public function index(GroupFilters $filters)
    {
        return response($filters->apply(Faculty::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function show(Faculty $faculty)
    {
        return $faculty;
    }
}
