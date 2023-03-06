<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'title',
        'notes',
        'professor_id',
        'department_id',
        'professor',        // backward compatibility
    ];

    public function professor()
    {
        return $this->belongsTo(User::class);
    }

    public function scheduleSubjects()
    {
        return $this->hasMany(ScheduleSubject::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public $timestamps = false;
}
