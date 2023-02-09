<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
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
        'program_id',
    ];

    public $timestamps = false;

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function specializations()
    {
        return $this->hasMany(Specialization::class);
    }
}
