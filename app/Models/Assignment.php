<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assignment extends Model
{
    use Auditable, HasFactory;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeLatestRelevant(Builder $query): Builder
    {
        return $query
            ->orderByRaw('CASE WHEN supervisor_id IS NULL THEN 1 ELSE 0 END')
            ->orderByRaw('CASE WHEN ojt_adviser_id IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('updated_at')
            ->orderByDesc('start_date')
            ->orderByDesc('id');
    }

    public static function resolveActiveForStudent(int $studentId, ?int $supervisorId = null, ?int $adviserId = null): ?self
    {
        $query = self::query()
            ->where('student_id', $studentId)
            ->active();

        if ($supervisorId !== null) {
            $query->where('supervisor_id', $supervisorId);
        }

        if ($adviserId !== null) {
            $query->where('ojt_adviser_id', $adviserId);
        }

        $exactMatch = (clone $query)
            ->latestRelevant()
            ->first();

        if ($exactMatch) {
            return $exactMatch;
        }

        $withSupervisor = self::query()
            ->where('student_id', $studentId)
            ->active()
            ->whereNotNull('supervisor_id')
            ->latestRelevant()
            ->first();

        if ($withSupervisor) {
            return $withSupervisor;
        }

        return self::query()
            ->where('student_id', $studentId)
            ->active()
            ->latestRelevant()
            ->first();
    }

    public static function resolveActiveForSupervisorStudent(int $supervisorId, int $studentId): ?self
    {
        return self::resolveActiveForStudent($studentId, supervisorId: $supervisorId);
    }

    public static function resolveActiveForAdviserStudent(int $adviserId, int $studentId): ?self
    {
        return self::resolveActiveForStudent($studentId, adviserId: $adviserId);
    }

    public static function rosterForSupervisor(int $supervisorId, array $with = []): Collection
    {
        return self::query()
            ->with($with)
            ->where('supervisor_id', $supervisorId)
            ->active()
            ->whereHas('student', fn (Builder $q) => $q->eligibleStudentForRoster())
            ->latestRelevant()
            ->get()
            ->unique('student_id')
            ->values();
    }

    public static function rosterForAdviser(int $adviserId, array $with = []): Collection
    {
        return self::query()
            ->with($with)
            ->where('ojt_adviser_id', $adviserId)
            ->active()
            ->whereHas('student', fn (Builder $q) => $q->eligibleStudentForRoster())
            ->latestRelevant()
            ->get()
            ->unique('student_id')
            ->values();
    }

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'coordinator_id',
        'ojt_adviser_id',
        'company_id',
        'start_date',
        'end_date',
        'status',
        'required_hours',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'required_hours' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function ojtAdviser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ojt_adviser_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function workLogs(): HasMany
    {
        return $this->hasMany(WorkLog::class);
    }

    public function totalApprovedHours(): float
    {
        return (float) $this->workLogs()
            ->where('status', 'approved')
            ->sum('hours');
    }

    public function progressPercentage(): float
    {
        if ($this->required_hours <= 0) {
            return 0;
        }

        $total = $this->totalApprovedHours();
        $percentage = ($total / $this->required_hours) * 100;

        return min(100, round($percentage, 1));
    }
}
