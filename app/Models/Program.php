<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'speciality_id',
    ];

    public function speciality()
    {
        return $this->belongsTo(Speciality::class);
    }

    public $timestamps = false;

    public function departments()
    {
        return $this->hasMany(Department::class);
    }
}
