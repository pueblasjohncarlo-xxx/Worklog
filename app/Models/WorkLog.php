<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkLog extends Model
{
    use Auditable, HasFactory;

    protected $table = 'work_logs';

    protected $fillable = [
        'assignment_id',
        'type',
        'work_date',
        'time_in',
        'time_out',
        'hours',
        'description',
        'skills_applied',
        'reflection',
        'attachment_path',
        'attachment_disk',
        'status',
        'submitted_to',
        'grade',
        'reviewer_comment',
        'adviser_comment',
        'reviewer_id',
        'reviewed_at',
    ];

    protected $casts = [
        'work_date' => 'date',
        'time_in' => 'datetime:H:i:s',
        'time_out' => 'datetime:H:i:s',
        'hours' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
