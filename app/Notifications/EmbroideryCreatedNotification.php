<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmbroideryCreatedNotification extends Notification
{
    use Queueable;
    public $embroidery;

    /**
     * Create a new notification instance.
     */
    public function __construct($embroidery)
    {
        $this->embroidery = $embroidery;
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "A new embroidery report has been created for your order: " . ($this->embroidery->order->style_no ?? ''),
            'embroidery_id' => $this->embroidery->id,
            'order_id' => $this->embroidery->order_id,
            'date' => $this->embroidery->date,
        ];
    }
}
