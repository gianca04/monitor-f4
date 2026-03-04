<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class GeneralPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly ?string $url = null,
        private readonly ?string $tag = null,
        private readonly ?string $icon = 'heroicon-o-bell',
        private readonly ?string $iconColor = 'primary',
        private readonly ?string $status = 'info',
    ) {}

    /**
     * Channels used for delivery.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->pushSubscriptions()->exists()) {
            $channels[] = WebPushChannel::class;
        }

        return $channels;
    }

    /**
     * Filament-compatible database notification.
     *
     * Filament v4 requires 'format' => 'filament' to display
     * notifications in the bell icon dropdown.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(mixed $notifiable): array
    {
        return [
            'format'    => 'filament',
            'duration'  => 'persistent',
            'title'     => $this->title,
            'body'      => $this->body,
            'icon'      => $this->icon,
            'iconColor' => $this->iconColor,
            'status'    => $this->status,
            'actions'   => $this->buildActions(),
        ];
    }

    /**
     * Fallback array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Web Push notification representation.
     */
    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/images/favicon.svg')
            ->badge('/images/favicon.svg')
            ->tag($this->tag ?? 'general')
            ->data(['url' => $this->url ?? '/dashboard']);
    }

    /**
     * Build Filament-compatible actions array.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildActions(): array
    {
        if (! $this->url) {
            return [];
        }

        return [
            [
                'name'       => 'view',
                'label'      => 'Ver',
                'url'        => $this->url,
                'isOpenUrlInNewTab' => false,
                'color'      => null,
                'icon'       => null,
                'iconPosition' => null,
                'size'       => null,
                'tooltip'    => null,
                'view'       => 'filament-actions::link-action',
            ],
        ];
    }
}
