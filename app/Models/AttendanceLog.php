<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'schedule_subject_id',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduleSubject()
    {
        return $this->belongsTo(ScheduleSubject::class);
    }

    public $timestamps = false;
}
