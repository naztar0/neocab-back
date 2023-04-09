<?php

namespace App\Services;

use App\Models\Rating;
use Illuminate\Http\UploadedFile;
use App\Models\User;

class UserService
{
    public function __construct(private readonly AzureApiService $azureService)
    {
    }

    public function getProfile(User $user): ?array
    {
        $profile = $user->with('group.specialization.department.program.speciality.faculty')
            ->where('id', $user->id)->first()->toArray();
        if (!$profile['image'] && $user->azure_id) {
            $profile['image'] = $this->azureService->getUserPhoto($user->azure_id);
        }

        return $profile;
    }

    public function getPhoto(User $user): ?string
    {
        if (!$user->azure_id) {
            return $user->image;
        }
        return $this->azureService->getUserPhoto($user->azure_id, 240);
    }

    public function getRecordBook(User $user, int $semester): array
    {
        return $user->recordBooks()->with('syllabus.subject.department')
            ->whereHas('syllabus', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })->get()->toArray();
    }

    public function getRating(User $user, int $semester): array
    {
        $speciality_id = $user->group->specialization->department->program->speciality->id;
        $result = Rating::query()->where('semester', $semester)
            ->with('user.group.specialization.department.program.speciality')
            ->where(function($query) use ($speciality_id) {
                $query->whereHas('user.group.specialization.department.program.speciality', function($q) use ($speciality_id) {
                    $q->where('id', $speciality_id);
                })->orWhere('speciality_id', $speciality_id);
            })
            ->orderBy('mark', 'desc')->get()->toArray();

        if ($user->payment == User::PAYMENT_CONTRACT) {
            $result = $this->getContractRating($user, $semester, $result);
        }

        return $result;
    }

    public function getSyllabus(User $user, int $semester): array
    {
        return $user->group->syllabuses()->with('subject.department')
            ->where('semester', $semester)->get()->toArray();
    }

    public function getAzureSchedule(User $user): array
    {
        $schedule = [];
        $events = $this->azureService->getUserCalendarEvents($user);
        foreach ($events as $event) {
            $organizer = '';
            foreach ($event->getAttendees() as $attendee) {
                $name = $attendee['emailAddress']['name'];
                if ($attendee['type'] == 'required' && count(explode(' ', $name)) == 3) {
                    $organizer = $name;
                    break;
                }
            }
            $joinUrl = $event->getOnlineMeeting() ? $event->getOnlineMeeting()->getJoinUrl() : null;
            $schedule[] = [
                'subject' => $event->getSubject(),
                'start' => $event->getStart()->getDateTime(),
                'end' => $event->getEnd()->getDateTime(),
                'organizer' => $organizer,
                'joinUrl' => $joinUrl,
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

    private function getContractRating(User $user, int $semester, array $result): array
    {
        $user_record_book = $user->recordBooks()->with('syllabus.subject.department')
            ->whereHas('syllabus', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })->get();
        $total_credits = $user_record_book->sum('syllabus.credit');

        if (!$total_credits) {
            return $result;
        }

        $coefficient = 0.9;
        $total_mark = 0;
        $comment = '(';

        foreach ($user_record_book as $record) {
            $total_mark += ($record->mark ?? 0) * $record->syllabus->credit;
            $comment .= " ($record->mark × " . $record->syllabus->credit . ') +';
        }

        $comment = substr($comment, 0, -1) . ") × $coefficient / $total_credits";
        $total_mark = round($total_mark * $coefficient / $total_credits, 2);
        $data = ['user_id' => $user->id, 'mark' => strval($total_mark), 'comment' => $comment];

        for ($i = 0; $i < count($result); $i++) {
            if ($result[$i]['mark'] <= $total_mark) {
                break;
            }
        }

        return array_merge(array_slice($result, 0, $i), [$data], array_slice($result, $i));
    }
}
