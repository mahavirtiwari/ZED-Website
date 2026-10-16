<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Conventions of the existing ZED tables:
 * - created_by / modified_by user ids (timestamps: models set CREATED_AT /
 *   UPDATED_AT, mostly created_on / modified_on);
 * - status: 1 = active, 0 = inactive, 2 = deleted (rows are never removed).
 *
 * Models set $table and use this trait. The "notDeleted" global scope hides
 * status 2 everywhere, so a CMS "delete" is an update to status 2.
 */
trait ZedContent
{
    public const STATUS_INACTIVE = 0;

    public const STATUS_ACTIVE = 1;

    public const STATUS_DELETED = 2;

    public static function bootZedContent(): void
    {
        static::addGlobalScope('notDeleted', function (Builder $query) {
            $query->where($query->getModel()->getTable().'.status', '!=', self::STATUS_DELETED);
        });

        static::creating(function ($model) {
            if ($model->usesAuditColumns()) {
                $model->created_by = Auth::id() ?? 0;
                $model->modified_by = $model->modified_by ?? 0;
            }
        });

        static::updating(function ($model) {
            if ($model->usesAuditColumns()) {
                $model->modified_by = Auth::id() ?? 0;
            }
        });
    }

    public function initializeZedContent(): void
    {
        $this->mergeCasts(['status' => 'integer']);
    }

    /** Most tables have created_by / modified_by; override to return false where they do not. */
    public function usesAuditColumns(): bool
    {
        return true;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', self::STATUS_ACTIVE);
    }

    /** Soft delete the ZED way: status 2. */
    public function markDeleted(): bool
    {
        $this->status = self::STATUS_DELETED;

        return $this->save();
    }
}
