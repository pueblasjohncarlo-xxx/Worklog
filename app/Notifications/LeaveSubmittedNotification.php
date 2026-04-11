<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public Leave $leave)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $url = method_exists($notifiable, 'getAttribute') && $notifiable->role === 'admin'
            ? route('admin.dashboard')
            : route('supervisor.leaves.index');

        return [
            'type' => 'leave_submitted',
            'leave_id' => $this->leave->id,
            'title' => 'New Leave Request Submitted',
            'student_name' => $this->leave->assignment?->student?->name,
            'leave_type' => $this->leave->type,
            'dates' => $this->leave->start_date?->format('M d, Y').' - '.$this->leave->end_date?->format('M d, Y'),
            'url' => $url,
        ];
    }
}
