<?php

namespace App\Services;

use App\Filters\AttendanceFilters;
use App\Models\AttendanceLog;
use App\Models\Group;
use App\Models\Rating;
use App\Models\RecordBook;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Microsoft\Graph\Exception\GraphException;

class UserService
{
    public function __construct(private readonly AzureApiService $azureService)
    {
    }

    public function getProfile(User $user): ?array
    {
        $profile = $user->load('group.specialization.department.program.speciality.faculty')->toArray();

        if (!$profile['image']) {
            $profile['image'] = $this->syncPhoto($user);
        }

        return $profile;
    }

    public function syncPhoto(User $user): ?string
    {
        if (!$user->azure_id) {
            return null;
        }
        try {
            if ($imageStr = $this->azureService->getUserPhoto($user->azure_id)) {
                $image = $this->saveBase64Image($imageStr);
                $user->update(['image' => $image]);
                return $image;
            }
            throw new GraphException(null);
        } catch (GraphException) {
            return null;
        }
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
        $specialityId = $user->group->specialization->department->program->speciality->id;
        $result = Rating::query()->where('semester', $semester)
            ->with('user.group.specialization.department.program.speciality')
            ->where(function($query) use ($specialityId) {
                $query->whereHas(
                    'user.group.specialization.department.program.speciality',
                    function($q) use ($specialityId) {
                        $q->where('id', $specialityId);
                    })->orWhere('speciality_id', $specialityId);
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
        $image->move(storage_path('app/public/images'), $imageName);
        return $imageName;
    }

    private function saveBase64Image(string $base64): string
    {
        $image = base64_decode($base64);
        $imageName = Str::random(40) . '.jpg';
        $path = "images/$imageName";
        Storage::disk('public')->put($path, $image);
        return $imageName;
    }

    private function getContractRating(User $user, int $semester, array $result): array
    {
        $userRecordBook = $user->recordBooks()->with('syllabus.subject.department')
            ->where('mark', '>', 0)
            ->whereHas('syllabus', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })->get();
        $totalCredits = $userRecordBook->sum('syllabus.credit');

        if (!$totalCredits) {
            return $result;
        }

        $coefficient = 0.9;
        $totalMark = 0;
        $comment = '(';

        foreach ($userRecordBook as $record) {
            $totalMark += $record->mark * $record->syllabus->credit;
            $comment .= " ($record->mark × " . $record->syllabus->credit . ') +';
        }

        $comment = substr($comment, 0, -1) . ") × $coefficient / $totalCredits";
        $totalMark = round($totalMark * $coefficient / $totalCredits, 2);
        $data = [
            'user_id' => $user->id,
            'mark' => strval($totalMark),
            'comment' => $comment,
            'semester' => $semester,
            'user' => $user,
            'id' => 0,
            'name' => null,
            'group' => null,
            'student_id' => null,
            'speciality_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        for ($i = 0; $i < count($result); $i++) {
            if ($result[$i]['mark'] <= $totalMark) {
                break;
            }
        }

        return array_merge(array_slice($result, 0, $i), [$data], array_slice($result, $i));
    }

    public function getUserAttendanceLogs(User $user): array
    {
        $startDate = now()->startOfYear();
        $result = [];
        $result['attendance'] = $user->attendanceLogs()->with('scheduleSubject.subject')
            ->where('date', '>=', $startDate)
            ->orderByDesc('date')->get()->toArray();

        $result['history'] = $user->attendanceLogs()->select('date', DB::raw('COUNT(*) as count'))
            ->where('date', '>=', $startDate)
            ->groupBy('date')->orderByDesc('date')->get()->toArray();

        $result['most_visited'] = Subject::query()->select('subjects.title', DB::raw('COUNT(attendance_logs.id) as count'))
            ->join('schedule_subjects', 'subjects.id', '=', 'schedule_subjects.subject_id')
            ->join('attendance_logs', 'schedule_subjects.id', '=', 'attendance_logs.schedule_subject_id')
            ->where('attendance_logs.user_id', $user->id)
            ->where('attendance_logs.date', '>=', $startDate)
            ->groupBy('subjects.id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()->toArray();

        return $result;
    }

    public function getGroupAttendanceLogs(Group $group, AttendanceFilters $filters): array
    {
        $selectQuery = AttendanceLog::query()
            ->join('schedule_subjects', 'attendance_logs.schedule_subject_id', '=', 'schedule_subjects.id')
            ->join('users', 'attendance_logs.user_id', '=', 'users.id')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->join('subjects', 'schedule_subjects.subject_id', '=', 'subjects.id')
            ->join('schedules', 'schedule_subjects.schedule_id', '=', 'schedules.id')
            ->where('groups.id', $group->id)
            ->select(
                'attendance_logs.date',
                'schedule_subjects.*',
                'subjects.id as subject_id',
                'subjects.title as subject_title',
                'subjects.notes as subject_notes',
                'schedules.id as schedule_id',
                'schedules.group_id as schedule_group_id',
                'schedules.week as schedule_week',
                'schedules.description as schedule_description',
                'users.id as user_id',
                'users.email as user_email',
                'users.first_name as user_first_name',
                'users.last_name as user_last_name',
                'users.middle_name as user_middle_name',
                'users.student_id as user_student_id',
                'users.telegram_id as user_telegram_id',
                'users.azure_id as user_azure_id',
                'users.role as user_role',
            )
            ->orderByDesc('attendance_logs.date')
            ->get();

        $mappedQuery = $selectQuery->groupBy('date')->map(
            function($items) {
                $attendances = [];

                foreach ($items as $item) {
                    $subject = [
                        'id' => $item->subject_id,
                        'title' => $item->subject_title,
                        'notes' => $item->subject_notes,
                    ];
                    $schedule = [
                        'id' => $item->schedule_id,
                        'group_id' => $item->schedule_group_id,
                        'week' => $item->schedule_week,
                        'description' => $item->schedule_description,
                    ];
                    $user = [
                        'id' => $item->user_id,
                        'email' => $item->user_email,
                        'first_name' => $item->user_first_name,
                        'last_name' => $item->user_last_name,
                        'middle_name' => $item->user_middle_name,
                        'student_id' => $item->user_student_id,
                        'telegram_id' => $item->user_telegram_id,
                        'azure_id' => $item->user_azure_id,
                        'role' => $item->user_role,
                    ];

                    if (!array_key_exists($item->id, $attendances)) {
                        $attendances[$item->id] = [
                            'date' => $item->date,
                            'schedule_subject' => [
                                'id' => $item->id,
                                'time' => $item->time,
                                'day' => $item->day,
                                'notes' => $item->notes,
                                'subject_id' => $item->subject_id,
                                'subject' => $subject,
                                'schedule' => $schedule,
                            ],
                            'users' => [],
                        ];
                    }
                    $attendances[$item->id]['users'][] = $user;
                }
                return array_values($attendances);
            })->flatten(1);

        $result = new LengthAwarePaginator(
            $mappedQuery->forPage($filters->pageNum, $filters->itemsNum)->values(),
            $mappedQuery->count(),
            $filters->itemsNum,
            $filters->pageNum,
        );

        return $result->toArray();
    }

    public function getAcademicPerformance(User $user): array
    {
        $recordBookAverageMarksBySemester = RecordBook::query()
            ->join('syllabuses', 'record_books.syllabus_id', '=', 'syllabuses.id')
            ->select('syllabuses.semester', DB::raw('ROUND(AVG(mark), 2) as mark'))
            ->where('user_id', $user->id)
            ->groupBy('syllabuses.semester')
            ->orderBy('syllabuses.semester')
            ->get()->toArray();

        $specialityId = $user->group->specialization->department->program->speciality->id;

        $ratingTotalCountBuilder = Rating::query()
            ->selectRaw('COUNT(*)')
            ->whereRaw('semester = semester')
            ->where(function($query) use ($specialityId) {
                $query->whereHas(
                    'user.group.specialization.department.program.speciality',
                    function($q) use ($specialityId) {
                        $q->whereRaw("id = $specialityId");
                    })->orWhereRaw("speciality_id = $specialityId");
            });

        $ratingTotalCountQuery = $ratingTotalCountBuilder
            ->toSql();

        $ratingPositionQuery = $ratingTotalCountBuilder
            ->whereRaw("mark > (SELECT mark FROM ratings WHERE user_id = $user->id AND semester = semester)")
            ->toSql();

        if ($user->payment == $user::PAYMENT_BUDGET) {
            $ratingsBySemester = Rating::query()
                ->join('users', 'ratings.user_id', '=', 'users.id')
                ->join('groups', 'users.group_id', '=', 'groups.id')
                ->join('specializations', 'groups.specialization_id', '=', 'specializations.id')
                ->join('departments', 'specializations.department_id', '=', 'departments.id')
                ->join('programs', 'departments.program_id', '=', 'programs.id')
                ->join('specialities', 'programs.speciality_id', '=', 'specialities.id')
                ->select('semester',
                    DB::raw("($ratingPositionQuery) + 1 AS position"),
                    DB::raw("($ratingTotalCountQuery) AS total_count"))
                ->where('specialities.id', $specialityId)
                ->groupBy('semester')
                ->orderBy('semester')
                ->get()->toArray();
            foreach ($ratingsBySemester as &$rating) {
                $rating['percentage'] = round($rating['position'] * 100 / $rating['total_count']);
            }
        } else {
            $ratingsBySemester = [];
        }

        $attendances = $user->attendanceLogs()
            ->selectRaw('DATE_FORMAT(date, \'%Y-%m\') as date, COUNT(*) as count')
            ->groupByRaw('DATE_FORMAT(date, \'%Y-%m\')')
            ->orderByRaw('DATE_FORMAT(date, \'%Y-%m\')')
            ->get()->toArray();

        return [
            'record_book_marks' => $recordBookAverageMarksBySemester,
            'rating_marks' => $ratingsBySemester,
            'attendances' => $attendances,
        ];
    }
}
