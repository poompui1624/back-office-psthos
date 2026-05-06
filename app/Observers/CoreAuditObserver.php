<?php

namespace App\Observers;

use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;

class CoreAuditObserver
{
    public function created(Model $model): void
    {
        AuditLogService::log(
            action: 'created',
            model: $model,
            newValues: $model->getAttributes(),
            module: class_basename($model)
        );
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();

        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];

        foreach ($changes as $key => $value) {
            $oldValues[$key] = $model->getOriginal($key);
        }

        AuditLogService::log(
            action: 'updated',
            model: $model,
            oldValues: $oldValues,
            newValues: $changes,
            module: class_basename($model)
        );
    }

    public function deleted(Model $model): void
    {
        AuditLogService::log(
            action: 'deleted',
            model: $model,
            oldValues: $model->getOriginal(),
            module: class_basename($model)
        );
    }
}
