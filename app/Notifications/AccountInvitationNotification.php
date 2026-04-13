<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountInvitationNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $temporaryPassword,
        private readonly string $createdByName,
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
        return (new MailMessage)
            ->subject('Your WorkLog account is ready')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('An account has been created for you in WorkLog by '.$this->createdByName.'.')
            ->line('Role: '.str_replace('_', ' ', (string) $notifiable->role))
            ->line('Email: '.$notifiable->email)
            ->line('Temporary password: '.$this->temporaryPassword)
            ->line('Please sign in and change your password as soon as possible.')
            ->action('Open WorkLog', url('/login'));
    }
}
