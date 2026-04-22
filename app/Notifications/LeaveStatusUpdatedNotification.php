<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Leave $leave)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->leave->status);
        $studentName = $this->leave->assignment?->student?->name ?? 'Student';
        
        $message = (new MailMessage)
            ->subject("Your Leave Request has been {$status}")
            ->greeting("Hello {$studentName},")
            ->line("Your leave request has been **{$status}**.")
            ->line("**Leave Type:** {$this->leave->type}")
            ->line("**Dates:** {$this->leave->start_date?->format('M d, Y')} - {$this->leave->end_date?->format('M d, Y')}")
            ->line("**Days:** {$this->leave->number_of_days}");

        if ($this->leave->reviewer_remarks) {
            $message->line("**Reviewer Remarks:** {$this->leave->reviewer_remarks}");
        }

        return $message
            ->action('View Dashboard', route('student.dashboard'))
            ->line('Please log in to your account to view more details.')
            ->salutation('Best regards,\nWorkLog System');
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
            'url' => route('student.dashboard'),
        ];
    }
}
