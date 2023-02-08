<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Cathedra;
use App\Models\Specialization;
use App\Models\Group;
use App\Filters\UserFilters;
use App\Http\Requests\UploadImageRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }
    public function index(UserFilters $filters): Response
    {
        return response($filters->apply(User::class)
            ->paginate($filters->itemsNum, ['*'], 'page', $filters->pageNum));
    }

    public function getProfile(User $user): Response
    {
        return response($this->service->getProfile($user));
    }

    public function getPhoto(User $user): Response
    {
        return response($this->service->getPhoto($user));
    }

    public function getRecordBook(User $user, int $semester): Response
    {
        return response($this->service->getRecordBook($user, $semester));
    }

    public function getRating(User $user, int $semester): Response
    {
        return response($this->service->getRating($user, $semester));
    }

    public function getSyllabus(User $user, int $semester): Response
    {
        return response($this->service->getSyllabus($user, $semester));
    }

    public function uploadAvatar(UploadImageRequest $request, User $user, UserService $userService): Response
    {
        return response(tap($user)->update(['image' => $userService->saveImage($request->image)]));
    }

    public function uploadCover(UploadImageRequest $request, User $user, UserService $userService): Response
    {
        return response(tap($user)->update(['cover' => $userService->saveImage($request->image)]));
    }

    public function getGroupUsers(Group $group, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($group->users()->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecializationUsers(Specialization $specialization, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($specialization->groups()->with('users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getCathedraUsers(Cathedra $cathedra, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($cathedra->specializations()->with('groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramUsers(Program $program, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($program->cathedras()->with('specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityUsers(Speciality $speciality, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($speciality->programs()->with('cathedras.specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyUsers(Faculty $faculty, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($faculty->specialities()->with('programs.cathedras.specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
