<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'question_en',
        'question_ru',
        'question_uk',
        'answer_en',
        'answer_ru',
        'answer_uk',
    ];

    public $timestamps = false;
}
