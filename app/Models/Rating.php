<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'semester',
        'mark',
        'comment',
        'name',         // backward compatibility
        'group',        // backward compatibility
        'student_id',   // backward compatibility
        'speciality_id' // backward compatibility
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
