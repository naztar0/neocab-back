<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Syllabus extends Model
{
    use HasFactory;

    protected $table = 'syllabuses';

    protected $fillable = [
        'subject_id',
        'group_id',
        'semester',
        'hours',
        'credit',
        'control',
        'individual_task',
    ];

    const CONTROL_EXAM = 'exam';
    const CONTROL_CREDIT = 'credit';

    const INDIVIDUAL_TASK_COURSEWORK = 'coursework';
    const INDIVIDUAL_TASK_ESSAY = 'essay';
    const INDIVIDUAL_TASK_TEST = 'test';

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function recordBooks()
    {
        return $this->hasMany(RecordBook::class);
    }

    public $timestamps = false;
}
