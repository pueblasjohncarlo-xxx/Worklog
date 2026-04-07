<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    public $announcement;

    /**
     * Create a new notification instance.
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
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
        $url = route('dashboard');

        if ($notifiable->role === 'student') {
            $url = route('student.announcements.index');
        } elseif ($notifiable->role === 'supervisor') {
            $url = route('supervisor.announcements.index');
        } elseif ($notifiable->role === 'coordinator') {
            $url = route('coordinator.announcements.index');
        }

        return [
            'type' => 'announcement',
            'announcement_id' => $this->announcement->id,
            'title' => 'New Announcement: '.$this->announcement->title,
            'content' => \Illuminate\Support\Str::limit($this->announcement->content, 50),
            'url' => $url,
        ];
    }
}
