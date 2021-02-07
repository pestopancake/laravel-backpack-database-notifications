<?php

namespace Pestopancake\LaravelBackpackNotifications\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class DatabaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $type; // info / success / warning / error
    public $message;
    public $messageLong; // optional
    public $href; // optional
    public $hrefText; // optional

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        $message,
        $type = 'info',
        $messageLong = null,
        $href = null,
        $hrefText = null
    ) {
        $this->message = $message;
        $this->type = $type;
        $this->messageLong = $messageLong;
        $this->href = $href;
        $this->hrefText = $hrefText;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'type' => $this->type,
            'message_long' => $this->messageLong,
            'action_href' => $this->href,
            'action_text' => $this->hrefText,
        ];
    }
}
