<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use App\Models\User;

class UserService
{
    public function __construct(
        private readonly KhpiApiService  $khpiService,
        private readonly AzureApiService $azureService,
    )
    {
    }

    public function getProfile(User $user): ?array
    {
        $data = $user->toArray();
        $khpiData = $this->khpiService->page1($user);
        if (!$khpiData) {
            return null;
        }
        if (!$data['image']) {
            $data['image'] = $this->azureService->getUserPhoto($user->azure_id);
        }
        $data['year'] = $khpiData['kurs'];
        $data['middle_name'] = $khpiData['otch'];
        $data['train_level'] = $khpiData['train_level'];
        $data['train_form'] = $khpiData['train_form'];
        $data['payment'] = $khpiData['oplata'];
        $data['group'] = [
            'id' => $user->group->id,
            'name' => $user->group->name
        ];
        $data['specialization'] = [
            'id' => $user->group->specialization->id,
            'name' => $user->group->specialization->name
        ];
        $data['department'] = [
            'id' => $user->group->specialization->department->id,
            'name' => $user->group->specialization->department->name
        ];
        $data['program'] = [
            'id' => $user->group->specialization->department->program->id,
            'name' => $user->group->specialization->department->program->name
        ];
        $data['speciality'] = [
            'id' => $user->group->specialization->department->program->speciality->id,
            'name' => $user->group->specialization->department->program->speciality->name
        ];
        $data['faculty'] = [
            'id' => $user->group->specialization->department->program->speciality->faculty->id,
            'name' => $user->group->specialization->department->program->speciality->faculty->name
        ];

        return $data;
    }

    public function getPhoto(User $user): ?string
    {
        return $this->azureService->getUserPhoto($user->azure_id, 240);
    }

    public function getRecordBook(User $user, int $semester): array
    {
        $data = [];
        $recordBook = [];
        $debts = [];
        $khpiData = $this->khpiService->page2($user, $semester);
        foreach ($khpiData as $item) {
            $recordBook[] = [
                'subject' => [
                    'id' => $item['subj_id'],
                    'name' => trim($item['subject']),
                ],
                'professor' => trim($item['prepod']),
                'department' => [
                    'full' => trim($item['kafedra']),
                    'short' => trim($item['kabr']),
                ],
                'individual_task' => $item['indzav'],
                'mark_5' => $item['oc_short'],
                'mark_100' => $item['oc_bol'],
                'mark_ects' => $item['oc_ects'],
                'mark_national' => $item['oc_naz'],
                'credit' => $item['credit'],
                'control' => $item['control'],
                'debt' => $item['if_hvost'],
            ];
        }

        $khpiData = $this->khpiService->page3($user);
        foreach ($khpiData as $item) {
            $debts[] = [
                'subject' => [
                    'id' => intval($item['subj_id']),
                    'name' => trim($item['subject']),
                ],
                'professor' => trim($item['prepod']),
                'department' => [
                    'full' => trim($item['kafedra']),
                    'short' => trim($item['kabr']),
                ],
                'individual_task' => $item['indzav'],
                'semester' => intval($item['semestr']),
                'credit' => $item['credit'],
                'control' => $item['control'],
                'date' => $item['data'],
            ];
        }

        $data['record_book'] = $recordBook;
        $data['debts'] = $debts;

        return $data;
    }

    public function getRating(User $user, int $semester): array
    {
        $data = [];
        $khpiData = $this->khpiService->page5($user, $semester);
        foreach ($khpiData as $item) {
            $data[] = [
                'student' => [
                    'id' => intval($item['studid']),
                    'name' => trim($item['fio'])
                ],
                'group' => $item['group'],
                'position' => $item['n'],
                'mark_5' => $item['sbal5'],
                'mark_100' => $item['sbal100'],
                'calculation' => $item['rating'],
            ];
        }

        return $data;
    }

    public function getSyllabus(User $user, int $semester): array
    {
        $data = [];
        $khpiData = $this->khpiService->page4($user, $semester);
        foreach ($khpiData as $item) {
            $data[] = [
                'subject' => [
                    'id' => intval($item['subj_id']),
                    'name' => trim($item['subject']),
                ],
                'department' => [
                    'full' => trim($item['kafedra']),
                    'short' => trim($item['kabr']),
                ],
                'individual_task' => $item['indzav'],
                'credit' => $item['credit'],
                'control' => $item['control'],
                'semester' => $item['semestr'],
                'hours' => $item['audit'],
            ];
        }

        return $data;
    }

    public function getAzureSchedule(User $user): array
    {
        $schedule = [];
        $events = $this->azureService->getUserCalendarEvents($user);
        foreach ($events as $event) {
            $schedule[] = [
                'subject' => $event->getSubject(),
                'start' => $event->getStart()->getDateTime(),
                'end' => $event->getEnd()->getDateTime(),
            ];
        }

        return $schedule;
    }

    public function saveImage(UploadedFile $image): string
    {
        $imageName = $image->hashName();
        $image->move(public_path('storage/images'), $imageName);
        return $imageName;
    }
}
