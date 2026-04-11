<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Auditable, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_COORDINATOR = 'coordinator';

    public const ROLE_SUPERVISOR = 'supervisor';

    public const ROLE_STUDENT = 'student';

    public const ROLE_OJT_ADVISER = 'ojt_adviser';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'lastname',
        'firstname',
        'middlename',
        'age',
        'gender',
        'email',
        'password',
        'encrypted_password',
        'role',
        'status',
        'is_approved',
        'has_requested_account',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejection_reason',
        'section',
        'department',
        'last_login_at',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_approved' => 'boolean',
            'has_requested_account' => 'boolean',
        ];
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function supervisorProfile(): HasOne
    {
        return $this->hasOne(SupervisorProfile::class);
    }

    public function coordinatorProfile(): HasOne
    {
        return $this->hasOne(CoordinatorProfile::class);
    }

    public function ojtAdviserProfile(): HasOne
    {
        return $this->hasOne(OjtAdviserProfile::class, 'user_id');
    }

    public function studentAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'student_id');
    }

    public function supervisorAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'supervisor_id');
    }

    public function coordinatorAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'coordinator_id');
    }

    public function ojtAdviserAssignments(): HasMany
    {
        return $this->hasMany(Assignment::class, 'ojt_adviser_id');
    }

    public function workLogs(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkLog::class,
            Assignment::class,
            'student_id',
            'assignment_id'
        );
    }

    public function reviewedWorkLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class, 'reviewer_id');
    }
}
