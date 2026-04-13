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

    public const ROLE_STAFF = 'staff';

    public const ROLE_COORDINATOR = 'coordinator';

    public const ROLE_SUPERVISOR = 'supervisor';

    public const ROLE_STUDENT = 'student';

    public const ROLE_OJT_ADVISER = 'ojt_adviser';

    public const STUDENT_SECTION_BSIT_4A = 'BSIT-4A';

    public const STUDENT_SECTION_BSIT_4B = 'BSIT-4B';

    public const STUDENT_SECTION_BSIT_4C = 'BSIT-4C';

    public const STUDENT_SECTION_BSIT_4D = 'BSIT-4D';

    public const STUDENT_SECTION_BSIT_4AE = 'BSIT-4AE';

    public const STUDENT_MAJOR_COMPUTER_TECHNOLOGY = 'Computer Technology';

    public const STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY = 'Electronics Technology';

    public const STUDENT_SECTIONS = [
        self::STUDENT_SECTION_BSIT_4A,
        self::STUDENT_SECTION_BSIT_4B,
        self::STUDENT_SECTION_BSIT_4C,
        self::STUDENT_SECTION_BSIT_4D,
        self::STUDENT_SECTION_BSIT_4AE,
    ];

    public const STUDENT_MAJORS = [
        self::STUDENT_MAJOR_COMPUTER_TECHNOLOGY,
        self::STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY,
    ];

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

    public static function normalizeStudentDepartment(?string $department): ?string
    {
        $raw = trim((string) $department);
        if ($raw === '') {
            return null;
        }

        $normalized = strtoupper(preg_replace('/[^a-zA-Z0-9 ]+/', ' ', $raw));
        $normalized = preg_replace('/\s+/', ' ', $normalized);

        if (str_contains($normalized, 'ELECTRONIC')) {
            return self::STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY;
        }

        if (str_contains($normalized, 'COMPUTER') || str_contains($normalized, 'COMPTECH') || str_contains($normalized, 'COT')) {
            return self::STUDENT_MAJOR_COMPUTER_TECHNOLOGY;
        }

        return null;
    }

    public static function normalizeStudentSection(?string $section, ?string $department = null): ?string
    {
        $raw = trim((string) $section);
        $normalizedDepartment = self::normalizeStudentDepartment($department);

        if ($raw !== '') {
            $upper = strtoupper($raw);
            $compact = preg_replace('/[^A-Z0-9]+/', '', $upper);

            if (str_contains($compact, 'NOSECTION')) {
                $upper = '';
                $compact = '';
            }

            if (preg_match('/(3|4)(AE|A|B|C|D)/', $compact, $m)) {
                return 'BSIT-4'.$m[2];
            }

            if (str_contains($compact, '4AE') || str_contains($compact, 'BSIT4AE')) {
                return self::STUDENT_SECTION_BSIT_4AE;
            }

            if (preg_match('/(^|[^0-9])4A([^A-Z0-9]|$)/', $upper) || str_contains($compact, 'BSIT4A')) {
                return self::STUDENT_SECTION_BSIT_4A;
            }

            if (preg_match('/(^|[^0-9])4B([^A-Z0-9]|$)/', $upper) || str_contains($compact, 'BSIT4B')) {
                return self::STUDENT_SECTION_BSIT_4B;
            }

            if (preg_match('/(^|[^0-9])4C([^A-Z0-9]|$)/', $upper) || str_contains($compact, 'BSIT4C')) {
                return self::STUDENT_SECTION_BSIT_4C;
            }

            if (preg_match('/(^|[^0-9])4D([^A-Z0-9]|$)/', $upper) || str_contains($compact, 'BSIT4D') || str_ends_with($compact, 'BSITD')) {
                return self::STUDENT_SECTION_BSIT_4D;
            }
        }

        if ($normalizedDepartment === self::STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY) {
            return self::STUDENT_SECTION_BSIT_4AE;
        }

        if ($normalizedDepartment === self::STUDENT_MAJOR_COMPUTER_TECHNOLOGY) {
            return self::STUDENT_SECTION_BSIT_4A;
        }

        return null;
    }

    public static function inferStudentDepartmentFromSection(?string $section): string
    {
        return $section === self::STUDENT_SECTION_BSIT_4AE
            ? self::STUDENT_MAJOR_ELECTRONICS_TECHNOLOGY
            : self::STUDENT_MAJOR_COMPUTER_TECHNOLOGY;
    }

    public function normalizedStudentSection(): ?string
    {
        return self::normalizeStudentSection($this->section, $this->department);
    }

    public function normalizedStudentDepartment(): ?string
    {
        $normalized = self::normalizeStudentDepartment($this->department);
        if ($normalized !== null) {
            return $normalized;
        }

        return self::inferStudentDepartmentFromSection($this->normalizedStudentSection());
    }
}
