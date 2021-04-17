<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPassword extends Notification
{

    public function toMail($notifiable)
    {
        $url = url(config('app.client_url') . '/password/reset/' . $this->token) . '?email=' . urlencode($notifiable->email);

        return (new MailMessage)
            ->line('Email for reset you password , you ask!')
            ->action('Reset Password', $url)
            ->line('If u did not ask reset password ,ignore this email');
    }
}
