<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\GeneralPushNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class PushNotificationService
{
    /**
     * Send a push notification to a single user.
     */
    public function sendToUser(
        User $user,
        string $title,
        string $body,
        ?string $url = null,
        ?string $tag = null,
        ?string $icon = 'heroicon-o-bell',
        ?string $iconColor = 'primary',
        ?string $status = 'info',
    ): void {
        $user->notify(new GeneralPushNotification($title, $body, $url, $tag, $icon, $iconColor, $status));
    }

    /**
     * Send a push notification to multiple users at once.
     *
     * @param  Collection<int, User>|array<int, User>  $users
     */
    public function sendToMany(
        Collection|array $users,
        string $title,
        string $body,
        ?string $url = null,
        ?string $tag = null,
        ?string $icon = 'heroicon-o-bell',
        ?string $iconColor = 'primary',
        ?string $status = 'info',
    ): void {
        $users = $users instanceof Collection ? $users : collect($users);

        Notification::send(
            $users,
            new GeneralPushNotification($title, $body, $url, $tag, $icon, $iconColor, $status)
        );
    }

    /**
     * Send a push notification to all active users.
     */
    public function broadcast(
        string $title,
        string $body,
        ?string $url = null,
        ?string $tag = null,
        ?string $icon = 'heroicon-o-bell',
        ?string $iconColor = 'primary',
        ?string $status = 'info',
    ): void {
        $users = User::where('is_active', true)->get();

        $this->sendToMany($users, $title, $body, $url, $tag, $icon, $iconColor, $status);
    }

    /**
     * Send a push notification to all users with a specific role.
     */
    public function sendToRole(
        string $role,
        string $title,
        string $body,
        ?string $url = null,
        ?string $tag = null,
        ?string $icon = 'heroicon-o-bell',
        ?string $iconColor = 'primary',
        ?string $status = 'info',
    ): void {
        $users = User::role($role)->where('is_active', true)->get();

        $this->sendToMany($users, $title, $body, $url, $tag, $icon, $iconColor, $status);
    }
}
