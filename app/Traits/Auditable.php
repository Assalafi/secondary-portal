<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->logAudit('created', $model->getAuditNewValues());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) return;

            $old = collect($model->getOriginal())->only(array_keys($dirty))->toArray();
            $new = collect($dirty)->toArray();

            // Skip logging if only timestamps changed
            $nonTimestampChanges = collect($dirty)->except(['updated_at', 'created_at']);
            if ($nonTimestampChanges->isEmpty()) return;

            $model->logAudit('updated', $new, $old);
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', null, $model->getAuditOldValues());
        });
    }

    protected function logAudit(string $event, ?array $newValues = null, ?array $oldValues = null): void
    {
        $module = $this->getAuditModule();
        $description = $this->getAuditDescription($event);

        // Filter out sensitive fields
        $sensitiveFields = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];
        if ($oldValues) {
            $oldValues = collect($oldValues)->except($sensitiveFields)->toArray();
        }
        if ($newValues) {
            $newValues = collect($newValues)->except($sensitiveFields)->toArray();
        }

        AuditLog::record($event, $module, $description, $this, $oldValues, $newValues);
    }

    protected function getAuditModule(): string
    {
        if (property_exists($this, 'auditModule')) {
            return $this->auditModule;
        }

        $className = class_basename($this);
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $className));
    }

    protected function getAuditDescription(string $event): string
    {
        $className = class_basename($this);
        $identifier = $this->getAuditIdentifier();

        return match ($event) {
            'created' => "{$className} {$identifier} was created",
            'updated' => "{$className} {$identifier} was updated",
            'deleted' => "{$className} {$identifier} was deleted",
            default => "{$className} {$identifier}: {$event}",
        };
    }

    protected function getAuditIdentifier(): string
    {
        if (property_exists($this, 'auditIdentifier')) {
            $field = $this->auditIdentifier;
            return $this->{$field} ?? "#{$this->id}";
        }

        // Try common identifier fields
        foreach (['name', 'title', 'admission_no', 'email', 'code'] as $field) {
            if ($this->{$field}) {
                return $this->{$field};
            }
        }

        return "#{$this->id}";
    }

    protected function getAuditNewValues(): array
    {
        return $this->getAttributes();
    }

    protected function getAuditOldValues(): array
    {
        return $this->getOriginal();
    }
}
