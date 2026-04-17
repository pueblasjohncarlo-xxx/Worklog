<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskReviewedNotification extends Notification
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
        $status = (string) ($this->task->status ?? 'updated');
        $title = $status === 'approved' ? 'Task approved' : ($status === 'rejected' ? 'Task rejected' : 'Task updated');
        $content = $title.': '.$this->task->title;
        if ($status === 'approved' && ! empty($this->task->grade)) {
            $content .= ' (Grade: '.$this->task->grade.')';
        }

        return [
            'type' => 'task_reviewed',
            'title' => $title,
            'content' => $content,
            'url' => route('student.tasks.show', ['taskId' => $this->task->id]),
            'task_id' => $this->task->id,
            'status' => $status,
        ];
    }
}
