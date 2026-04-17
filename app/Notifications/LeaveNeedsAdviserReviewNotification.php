<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeaveNeedsAdviserReviewNotification extends Notification
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
        $studentName = $this->leave->assignment?->student?->name ?? 'Student';

        return [
            'type' => 'leave_needs_adviser_review',
            'leave_id' => $this->leave->id,
            'title' => 'Leave needs adviser review',
            'student_name' => $studentName,
            'leave_type' => $this->leave->type,
            'status' => $this->leave->status,
            'url' => route('ojt_adviser.leaves.index'),
        ];
    }
}
