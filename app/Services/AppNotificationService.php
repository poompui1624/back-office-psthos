<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;

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

    /**
     * Active users holding a permission, ready to be notified.
     *
     * Spatie's permission scope throws when the permission row does not exist,
     * which would turn "notify the approvers" into a 500 on an install where the
     * permission has not been seeded yet. Nobody to notify is not an error, so
     * this returns an empty collection instead.
     *
     * @return Collection<int, User>
     */
    public static function activeUsersWithPermission(string $permission): Collection
    {
        $guard = config('auth.defaults.guard', 'web');

        $exists = Permission::query()
            ->where('name', $permission)
            ->where('guard_name', $guard)
            ->exists();

        if (! $exists) {
            return collect();
        }

        return User::permission($permission)
            ->where('is_active', true)
            ->get();
    }
}
