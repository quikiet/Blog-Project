<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    public $user;
    public function __construct($user)
    {
        $this->user = $user;
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
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject('Thông báo: Người dùng mới đăng ký')
            ->line("Người dùng mới đăng ký: {$this->user->name} (Email: {$this->user->email})")
            ->action('Truy cập trang', url('https://tqkdomain.io.vn/front-end/'))
            ->line('Cảm ơn bạn đã sử dụng trang web của tôi!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Người dùng mới đăng ký: {$this->user->name}",
            'type' => 'new_user',
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'avatar' => $this->user->avatar ?? '',
            ],
        ];
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
