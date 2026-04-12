<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit('created', $model);
        });

        static::updated(function ($model) {
            self::logAudit('updated', $model);
        });

        static::deleted(function ($model) {
            self::logAudit('deleted', $model);
        });
    }

    protected static function logAudit($action, $model)
    {
        try {
            $oldValues = null;
            $newValues = null;

            if ($action === 'updated') {
                $oldValues = $model->getOriginal();
                $newValues = $model->getChanges();
            } elseif ($action === 'created') {
                $newValues = $model->getAttributes();
            } elseif ($action === 'deleted') {
                $oldValues = $model->getAttributes();
            }

            AuditLog::create([
                'user_id' => Auth::id(), // Might be null if action is system-triggered or unauthenticated
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->id,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log audit failure but don't break the operation
            \Illuminate\Support\Facades\Log::warning('Failed to log audit action', [
                'action' => $action,
                'model' => get_class($model),
                'model_id' => $model->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
