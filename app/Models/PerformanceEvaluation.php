<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'evaluation_date',
        'semester',
        'attendance_punctuality',
        'quality_of_work',
        'initiative',
        'cooperation',
        'dependability',
        'communication_skills',
        'remarks',
        'final_rating',
        'submitted_at',
        'document_path',
        'document_type',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'final_rating' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }
}
