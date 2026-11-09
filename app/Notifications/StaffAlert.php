<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/** A general alert in the admin bell: new review, blog comment or contact message. */
class StaffAlert extends Notification
{
    use Queueable;

    public function __construct(
        public string $kind,
        public string $message,
        public string $url,
        public string $icon = 'bx-bell',
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->kind,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
