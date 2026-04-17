<?php

namespace App\Notifications;

use App\Models\WorkLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkLogSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public WorkLog $workLog)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->workLog->loadMissing(['assignment.student']);
        $studentName = $this->workLog->assignment?->student?->name ?? 'Student';
        $workDate = $this->workLog->work_date ? $this->workLog->work_date->format('M d, Y') : 'a date';

        return [
            'type' => 'worklog_submitted',
            'title' => 'Work log submitted',
            'content' => $studentName." submitted a work log for {$workDate}.",
            'url' => route('supervisor.accomplishment-reports', ['status' => 'submitted']),
            'worklog_id' => $this->workLog->id,
            'student_id' => $this->workLog->assignment?->student_id,
        ];
    }
}
