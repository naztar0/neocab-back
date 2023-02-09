<?php

namespace App\Services;

use App\Models\User;
use App\Models\Faculty;
use App\Models\Speciality;
use App\Models\Program;
use App\Models\Department;
use App\Models\Specialization;
use App\Models\Group;

class AuthService
{
    public function __construct(private readonly KhpiApiService $service)
    {
    }

    public function createUser(array $credentials)
    {
        $user = new User($credentials);
        $data = $this->service->page1($user);
        if (!$data) {
            return null;
        }
        return User::updateOrCreate([
            'email' => $user->email,
            ], ['email' => $user->email,
            'pass' => $user->pass,
            'student_id' => $data['st_cod'],
            'first_name' => $data['imya'],
            'last_name' => $data['fam'],
            'group_id' => $this->getOrCreateGroup($data),
        ]);
    }
    private function getOrCreateGroup(array $data)
    {
        $faculty = Faculty::firstOrCreate(['id' => $data['fid']], ['name' => $data['fakultet']]);
        $speciality = Speciality::firstOrCreate(['name' => $data['speciality']], ['faculty_id' => $faculty->id]);
        $program = Program::firstOrCreate(['name' => $data['osvitprog']], ['speciality_id' => $speciality->id]);
        $department = Department::firstOrCreate(['id' => $data['kid']], ['name' => $data['kafedra'], 'program_id' => $program->id]);
        $specialization = Specialization::firstOrCreate(['name' => $data['specialization']], ['department_id' => $department->id]);
        $group = Group::firstOrCreate(['id' => $data['gid']], ['name' => $data['grupa'], 'specialization_id' => $specialization->id]);
        return $group->id;
    }
}
