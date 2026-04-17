<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConcernReport extends Model
{
    protected $fillable = [
        'assignment_id',
        'supervisor_id',
        'student_id',
        'type',
        'title',
        'details',
        'occurred_on',
    ];

    protected $casts = [
        'occurred_on' => 'date',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
