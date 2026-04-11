<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveStatusUpdatedNotification extends Notification
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
        return [
            'type' => 'leave_status_updated',
            'leave_id' => $this->leave->id,
            'title' => 'Leave Request '.ucfirst($this->leave->status),
            'status' => $this->leave->status,
            'leave_type' => $this->leave->type,
            'remarks' => $this->leave->reviewer_remarks,
            'url' => route('student.leaves.index'),
        ];
    }
}
