<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RegistrationInvitationLinkNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $inviterName,
        private readonly string $role,
        private readonly string $registerUrl,
        private readonly ?string $companyName,
        private readonly \DateTimeInterface $expiresAt,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $roleLabel = match ($this->role) {
            User::ROLE_OJT_ADVISER => 'OJT Adviser',
            User::ROLE_SUPERVISOR => 'Supervisor',
            User::ROLE_STUDENT => 'Student',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };

        $mail = (new MailMessage)
            ->subject('WorkLog Registration Invitation')
            ->greeting('Hello,')
            ->line($this->inviterName.' invited you to register in WorkLog as '.$roleLabel.'.')
            ->line('Use the secure link below to complete your registration.')
            ->line('This invitation expires on '.$this->expiresAt->format('M d, Y h:i A').'.')
            ->action('Complete Registration', $this->registerUrl)
            ->line('If you were not expecting this invitation, you can ignore this email.');

        if (! is_null($this->companyName) && $this->companyName !== '') {
            $mail->line('Assigned company: '.$this->companyName);
        }

        return $mail;
    }
}
