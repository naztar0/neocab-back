<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSubject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'time',
        'day',
        'notes',
        'schedule_id',
        'subject_id',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public $timestamps = false;
}
