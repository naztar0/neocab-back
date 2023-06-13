<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'group_id',
        'week',
        'description',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function scheduleSubjects()
    {
        return $this->hasMany(ScheduleSubject::class);
    }

    public $timestamps = false;
}
