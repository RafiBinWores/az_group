<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CuttingCreatedNotification extends Notification
{
    use Queueable;

    public $cutting;

    /**
     * Create a new notification instance.
     */
    public function __construct($cutting)
    {
        $this->cutting = $cutting;
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
        return [
            'message' => "A new cutting report has been created for your order: " . ($this->cutting->order->style_no ?? ''),
            'cutting_id' => $this->cutting->id,
            'order_id' => $this->cutting->order_id,
            'date' => $this->cutting->date,
        ];
    }
}
