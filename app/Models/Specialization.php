<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'department_id',
    ];

    public $timestamps = false;

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
