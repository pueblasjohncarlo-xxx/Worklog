<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Task $task)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->task->loadMissing(['assignment.student']);
        $studentName = $this->task->assignment?->student?->name ?? 'Student';

        return [
            'type' => 'task_submitted',
            'title' => 'Task submitted',
            'content' => $studentName." submitted: {$this->task->title}",
            'url' => route('supervisor.dashboard'),
            'task_id' => $this->task->id,
            'assignment_id' => $this->task->assignment_id,
        ];
    }
}
