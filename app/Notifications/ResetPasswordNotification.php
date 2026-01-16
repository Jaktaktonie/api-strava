<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public function __construct(private readonly string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $email = $notifiable->getEmailForPasswordReset();
        $frontend = rtrim(config('app.frontend_url'), '/');
        $url = $frontend.'/password-reset/'.$this->token.'?email='.urlencode($email);

        return (new MailMessage())
            ->subject('Reset hasla')
            ->view('emails.password-reset', [
                'url' => $url,
                'email' => $email,
            ]);
    }
}
