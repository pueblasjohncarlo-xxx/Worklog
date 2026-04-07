<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification
{
    use Queueable;

    public $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_due_soon',
            'task_id' => $this->task->id,
            'title' => 'Task Due Soon: '.$this->task->title,
            'due_date' => $this->task->due_date->format('M d, Y'),
            'days_remaining' => now()->diffInDays($this->task->due_date, false),
            'url' => route('student.tasks.index'),
        ];
    }
}
