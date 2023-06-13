<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'student_id',
        'telegram_id',
        'azure_id',
        'group_id',
        'email',
        'pass',
        'first_name',
        'last_name',
        'middle_name',
        'role',
        'image',
        'cover',
        'locale',
        'year',
        'train_level',
        'train_form',
        'payment',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
        'pass',
    ];

    public const ROLE_STUDENT = 'student';
    public const ROLE_HEADMAN = 'headman';
    public const ROLE_PROFESSOR = 'professor';
    public const ROLE_ADMIN = 'admin';

    public const LOCALE_EN = 'en';
    public const LOCALE_RU = 'ru';
    public const LOCALE_UA = 'ua';

    public const TRAIN_LEVEL_BACHELOR = 'bachelor';
    public const TRAIN_LEVEL_MASTER = 'master';

    public const TRAIN_FORM_FULL_TIME = 'full-time';
    public const TRAIN_FORM_EXTRAMURAL = 'extramural';

    public const PAYMENT_BUDGET = 'budget';
    public const PAYMENT_CONTRACT = 'contract';

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function recordBooks(): HasMany
    {
        return $this->hasMany(RecordBook::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function contract(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
