<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'assignment_id',
        'type',
        'start_date',
        'end_date',
        'number_of_days',
        'reason',
        'attachment_path',
        'status',
        'supervisor_decision',
        'supervisor_reviewer_id',
        'supervisor_reviewed_at',
        'supervisor_reviewer_remarks',
        'submitted_at',
        'cancelled_at',
        'cancellation_reason',
        'reviewer_remarks',
        'reviewer_id',
        'reviewed_at',
        'signature_path',
        'student_name',
        'course_major',
        'year_section',
        'cellphone_no',
        'company_name',
        'date_filed',
        'job_designation',
        'prepared_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'number_of_days' => 'integer',
        'date_filed' => 'date',
        'submitted_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'supervisor_reviewed_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function supervisorReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_reviewer_id');
    }
}
