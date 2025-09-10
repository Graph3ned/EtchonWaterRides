<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return $this->buildMailMessage($this->resetUrl($notifiable));
    }

    /**
     * Get the reset URL for the given notifiable.
     */
    protected function resetUrl($notifiable)
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Build the mail message.
     */
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Password Reset - Etchon Water Rides')
            ->greeting('Hello!')
            ->line('We received a request to reset your password for your Etchon Water Rides account.')
            ->action('Reset Password', $url)
            ->line('If you didn\'t request this password reset, please ignore this email. Your password will remain unchanged.')
            ->line('This password reset link will expire in 60 minutes.')
            ->salutation('Best regards, Etchon Water Rides Team');
    }
}
