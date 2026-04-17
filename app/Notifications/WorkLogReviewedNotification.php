<?php

namespace App\Notifications;

use App\Models\WorkLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WorkLogReviewedNotification extends Notification
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
        $workDate = $this->workLog->work_date ? $this->workLog->work_date->format('M d, Y') : 'a date';
        $status = (string) ($this->workLog->status ?? 'updated');

        return [
            'type' => 'worklog_reviewed',
            'title' => 'Work log '.($status === 'approved' ? 'approved' : ($status === 'rejected' ? 'rejected' : 'updated')),
            'content' => "Your work log for {$workDate} was {$status}.",
            'url' => route('student.dashboard'),
            'worklog_id' => $this->workLog->id,
            'status' => $status,
        ];
    }
}
