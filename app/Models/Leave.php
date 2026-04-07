<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = [
        'assignment_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'status',
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
        'date_filed' => 'date',
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
