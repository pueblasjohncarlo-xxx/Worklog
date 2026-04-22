<?php

namespace App\Notifications;

use App\Models\Leave;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveSubmittedNotification extends Notification
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
        $studentName = $this->leave->assignment?->student?->name ?? 'A student';
        $leaveType = $this->leave->type ?? 'Leave';
        
        return (new MailMessage)
            ->subject("New Leave Request from {$studentName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new leave request has been submitted and is awaiting your review.")
            ->line("**Student:** {$studentName}")
            ->line("**Leave Type:** {$leaveType}")
            ->line("**Dates:** {$this->leave->start_date?->format('M d, Y')} - {$this->leave->end_date?->format('M d, Y')}")
            ->line("**Days:** {$this->leave->number_of_days}")
            ->when($this->leave->reason, function ($mail) {
                return $mail->line("**Reason:** {$this->leave->reason}");
            })
            ->action('Open Dashboard', route('supervisor.dashboard'))
            ->line('Please log in to your account for updated review workflows.')
            ->salutation('Best regards,\nWorkLog System');
    }

    public function toArray(object $notifiable): array
    {
        $url = method_exists($notifiable, 'getAttribute') && $notifiable->role === 'admin'
            ? route('admin.dashboard')
            : route('supervisor.dashboard');

        return [
            'type' => 'leave_submitted',
            'leave_id' => $this->leave->id,
            'title' => 'New Leave Request Submitted',
            'student_name' => $this->leave->assignment?->student?->name,
            'leave_type' => $this->leave->type,
            'dates' => $this->leave->start_date?->format('M d, Y').' - '.$this->leave->end_date?->format('M d, Y'),
            'days' => $this->leave->number_of_days,
            'url' => $url,
        ];
    }
}
