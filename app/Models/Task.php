<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema;

class Task extends Model
{
    use SoftDeletes;

    protected static function bootSoftDeletes(): void
    {
        if (Schema::hasColumn('tasks', 'deleted_at')) {
            static::addGlobalScope(new SoftDeletingScope);
        }
    }

    protected $fillable = [
        'assignment_id',
        'title',
        'description',
        'attachment_path',
        'original_filename',
        'task_attachment_path',
        'task_original_filename',
        'semester',
        'status',
        'grade',
        'supervisor_note',
        'supervisor_attachment_path',
        'supervisor_original_filename',
        'remarks',
        'due_date',
        'submitted_at',
        'unsubmitted_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'unsubmitted_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }
}
