<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Department;
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

    public function getRecordBook(User $user, int $semester): Response
    {
        $this->authorize('view', $user);
        return response($this->service->getRecordBook($user, $semester));
    }

    public function getRating(User $user, int $semester): Response
    {
        $this->authorize('view', $user);
        return response($this->service->getRating($user, $semester));
    }

    public function getSyllabus(User $user, int $semester): Response
    {
        $this->authorize('view', $user);
        return response($this->service->getSyllabus($user, $semester));
    }

    public function uploadAvatar(UploadImageRequest $request, User $user, UserService $userService): Response
    {
        $this->authorize('update', $user);
        return response(tap($user)->update(['image' => $userService->saveImage($request->image)]));
    }

    public function uploadCover(UploadImageRequest $request, User $user, UserService $userService): Response
    {
        $this->authorize('update', $user);
        return response(tap($user)->update(['cover' => $userService->saveImage($request->image)]));
    }

    public function syncPhoto(User $user): Response
    {
        $this->authorize('update', $user);
        return response($this->service->syncPhoto($user));
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

    public function getDepartmentUsers(Department $department, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($department->specializations()->with('groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getProgramUsers(Program $program, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($program->departments()->with('specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getSpecialityUsers(Speciality $speciality, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($speciality->programs()->with('departments.specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }

    public function getFacultyUsers(Faculty $faculty, UserFilters $filters): Response
    {
        $filters->apply(User::class);
        return response($faculty->specialities()->with('programs.departments.specializations.groups.users')->get()
            ->paginate($filters->itemsNum, $filters->pageNum));
    }
}
