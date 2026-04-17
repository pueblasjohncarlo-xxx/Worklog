<?php

namespace App\Models;

use App\Traits\Auditable;
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

    public static function resolveActiveForStudent(int $studentId): ?self
    {
        $withSupervisor = self::query()
            ->where('student_id', $studentId)
            ->active()
            ->whereNotNull('supervisor_id')
            ->latest('updated_at')
            ->first();

        if ($withSupervisor) {
            return $withSupervisor;
        }

        return self::query()
            ->where('student_id', $studentId)
            ->active()
            ->latest('updated_at')
            ->first();
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
