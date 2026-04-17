<?php

namespace App\Notifications;

use App\Models\WorkLog;
use App\Models\User;
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

        $url = route('supervisor.accomplishment-reports', ['status' => 'submitted']);
        $role = $notifiable->role ?? null;
        if ($role === User::ROLE_COORDINATOR) {
            $url = route('coordinator.accomplishment-reports');
        } elseif ($role === User::ROLE_OJT_ADVISER) {
            $url = route('ojt_adviser.accomplishment-reports');
        }

        return [
            'type' => 'worklog_submitted',
            'title' => 'Work log submitted',
            'content' => $studentName." submitted a work log for {$workDate}.",
            'url' => $url,
            'worklog_id' => $this->workLog->id,
            'student_id' => $this->workLog->assignment?->student_id,
        ];
    }
}
