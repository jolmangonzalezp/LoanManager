<?php

declare(strict_types=1);

namespace App\UserBC\Infrastructure\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PasswordResetNotification extends Notification
{
    public function __construct(
        private readonly string $token,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000'));
        $resetUrl = sprintf(
            '%s/reset-password?token=%s&email=%s',
            rtrim($frontendUrl, '/'),
            $this->token,
            $notifiable->getEmailForPasswordReset(),
        );

        return (new MailMessage)
            ->subject('Recuperacion de Contrasena')
            ->line('Estas recibiendo este correo porque solicitaste restablecer tu contrasena.')
            ->action('Restablecer Contrasena', $resetUrl)
            ->line('Este enlace expirara en 60 minutos.')
            ->line('Si no solicitaste este cambio, ignora este mensaje.');
    }
}
