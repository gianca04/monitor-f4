<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendPushNotification extends Command
{
    protected $signature = 'push:send
        {--user= : ID del usuario destinatario (omitir para broadcast)}
        {--title= : Título de la notificación}
        {--body= : Cuerpo de la notificación}
        {--url= : URL de destino al hacer clic}
        {--role= : Enviar a todos los usuarios con este rol}';

    protected $description = 'Enviar una notificación push a uno o más usuarios';

    public function handle(PushNotificationService $service): int
    {
        $title = $this->option('title') ?? $this->ask('Título de la notificación');
        $body  = $this->option('body') ?? $this->ask('Cuerpo de la notificación');
        $url   = $this->option('url') ?? '/dashboard';

        if ($userId = $this->option('user')) {
            $user = User::findOrFail($userId);
            $service->sendToUser($user, $title, $body, $url);
            $this->info("Notificación enviada a {$user->name}.");

            return self::SUCCESS;
        }

        if ($role = $this->option('role')) {
            $service->sendToRole($role, $title, $body, $url);
            $this->info("Notificación enviada a todos los usuarios con rol '{$role}'.");

            return self::SUCCESS;
        }

        $service->broadcast($title, $body, $url);
        $this->info('Notificación enviada a todos los usuarios activos.');

        return self::SUCCESS;
    }
}
