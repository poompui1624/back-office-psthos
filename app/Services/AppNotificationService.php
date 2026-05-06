<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;

class AppNotificationService
{
    public static function sendToUser(
        User $user,
        string $title,
        ?string $message = null,
        ?string $linkUrl = null,
        string $type = 'info',
        ?array $data = null
    ): AppNotification {
        return AppNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link_url' => $linkUrl,
            'data' => $data,
        ]);
    }

    public static function sendToUsers(
        Collection|array $users,
        string $title,
        ?string $message = null,
        ?string $linkUrl = null,
        string $type = 'info',
        ?array $data = null
    ): void {
        foreach ($users as $user) {
            self::sendToUser(
                user: $user,
                title: $title,
                message: $message,
                linkUrl: $linkUrl,
                type: $type,
                data: $data
            );
        }
    }
}