<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SoftwareLicense;
use App\Models\User;
use App\Services\AppNotificationService;
use Illuminate\Console\Command;

class CheckSoftwareLicenseExpirations extends Command
{
    protected $signature = 'software:check-expirations';

    protected $description = 'Check software licenses that are expiring soon or already expired.';

    public function handle(): int
    {
        $notifyDays = (int) (
            Setting::where('key', 'software.expire_notify_days')->value('value') ?: 30
        );

        $today = now()->startOfDay();
        $notifyUntil = now()->addDays($notifyDays)->endOfDay();

        $licenses = SoftwareLicense::query()
            ->with('product')
            ->where('status', 'active')
            ->whereNotNull('expire_date')
            ->whereDate('expire_date', '<=', $notifyUntil)
            ->where(function ($query) {
                $query->whereNull('last_expire_notified_at')
                    ->orWhereDate('last_expire_notified_at', '<', now()->toDateString());
            })
            ->orderBy('expire_date')
            ->get();

        if ($licenses->isEmpty()) {
            $this->info('No software licenses need notification.');

            return self::SUCCESS;
        }

        $users = User::permission('software.view')->where('is_active', true)->get();

        if ($users->isEmpty()) {
            $users = User::role('super_admin')->where('is_active', true)->get();
        }

        if ($users->isEmpty()) {
            $this->warn('No users found for software license notifications.');

            return self::SUCCESS;
        }

        foreach ($licenses as $license) {
            $productName = $license->product?->name ?? 'Unknown Software';
            $expireDate = $license->expire_date?->format('Y-m-d');

            $isExpired = $license->expire_date && $license->expire_date->lt($today);

            $title = $isExpired
                ? 'Software License หมดอายุแล้ว'
                : 'Software License ใกล้หมดอายุ';

            $message = $isExpired
                ? "{$productName} หมดอายุวันที่ {$expireDate}"
                : "{$productName} จะหมดอายุวันที่ {$expireDate}";

            foreach ($users as $user) {
                AppNotificationService::sendToUser(
                    user: $user,
                    title: $title,
                    message: $message,
                    linkUrl: route('software-licenses.index', [
                        'search' => $productName,
                    ]),
                    type: 'software_license',
                    data: [
                        'software_license_id' => $license->id,
                        'software_product_id' => $license->software_product_id,
                        'expire_date' => $expireDate,
                        'is_expired' => $isExpired,
                    ]
                );
            }

            $license->update([
                'last_expire_notified_at' => now(),
            ]);

            $this->info("Notification sent: {$productName} / {$expireDate}");
        }

        return self::SUCCESS;
    }
}
