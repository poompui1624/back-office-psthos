<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditLogService
{
    /**
     * Attributes stripped before a change is written to the audit log.
     *
     * The log is readable by anyone with audit.view, so credentials and national
     * identifiers must never land in old_values / new_values.
     *
     * @var array<int, string>
     */
    private const REDACTED_KEYS = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'citizen_id',
        'taxpayer_no',
        'social_security_no',
    ];

    public static function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $module = null
    ): void {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module ?? class_basename($model),
            'auditable_type' => get_class($model),
            'auditable_id' => $model->getKey(),
            'old_values' => self::cleanValues($oldValues),
            'new_values' => self::cleanValues($newValues),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'url' => request()?->fullUrl(),
        ]);
    }

    private static function cleanValues(?array $values): ?array
    {
        if ($values === null) {
            return null;
        }

        return Arr::except($values, self::REDACTED_KEYS);
    }
}
