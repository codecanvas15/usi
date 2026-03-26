<?php

namespace App\Models;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewNotifications extends Notification
{
    use Queueable;

    protected $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $data = [
            'icon' => $this->notification['icon'],
            'title' => $this->notification['title'],
            'text' => $this->notification['text'],
            'link' => $this->notification['links'],
            'menu' => $this->notification['menu'],
            'subject_id' => $this->notification['subject_id'],
        ];

        return $data;
    }
}
