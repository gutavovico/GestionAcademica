<?php

namespace App\AutenticacionYSeguridad\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $minutes = (int) config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->subject('Restablecer contraseña - Gestión Académica')
            ->greeting('Hola,')
            ->line('Recibimos una solicitud para restablecer la contraseña de tu cuenta.')
            ->line("Este enlace caduca en {$minutes} minutos.")
            ->action('Restablecer contraseña', $url)
            ->line('Si tú no solicitaste este cambio, puedes ignorar este correo.')
            ->line('Si el botón no funciona, copia y pega esta URL en tu navegador:')
            ->line($url);
    }
}

