<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'specialization_id',
    ];

    public function specialization()
    {
        return $this->belongsTo(Specialization::class);
    }

    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function syllabuses()
    {
        return $this->hasMany(Syllabus::class);
    }
}
