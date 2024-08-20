<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CheckTeacherMail extends Notification
{
    use Queueable;
    public $data;
    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        $url = URL::signedRoute('admin.adding.new.teacher', [
            'full_name' => $this->data['full_name'],
            'email' =>$this->data['email'],
            'phone' => $this->data['phone'],
            'password' => $this->data['password'],
            'birthday' => $this->data['birthday'],
            'address' => $this->data['address'],
            'type' => $this->data['type'],
            'image' => $this->data['image'],
        ]);
        return (new MailMessage)
            ->line('This user asked you about registering in your app. Do you want to add him/her?')
            ->action('Accept', $url)
            ->line('Or you can check the CV before making a decision:')
            ->action('Check CV', url('/'));

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
