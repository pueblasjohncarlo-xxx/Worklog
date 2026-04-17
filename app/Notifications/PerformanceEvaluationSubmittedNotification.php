<?php

namespace App\Notifications;

use App\Models\PerformanceEvaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PerformanceEvaluationSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public PerformanceEvaluation $evaluation, public ?string $url = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->evaluation->loadMissing(['student', 'supervisor']);

        $studentName = $this->evaluation->student?->name ?? 'Student';
        $supervisorName = $this->evaluation->supervisor?->name ?? 'Supervisor';
        $date = $this->evaluation->evaluation_date ? $this->evaluation->evaluation_date->format('M d, Y') : 'a date';

        return [
            'type' => 'evaluation_submitted',
            'title' => 'Performance evaluation submitted',
            'content' => "{$supervisorName} submitted an evaluation for {$studentName} ({$date}).",
            'url' => $this->url,
            'evaluation_id' => $this->evaluation->id,
            'student_id' => $this->evaluation->student_id,
            'supervisor_id' => $this->evaluation->supervisor_id,
        ];
    }
}
