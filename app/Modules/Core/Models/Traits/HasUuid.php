<?php

namespace App\Modules\Core\Models\Traits;

use Illuminate\Support\Str;

/**
 * @property string $uuid
 */
trait HasUuid
{
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function scopeUuid($query, $uuid)
    {
        return $query->where($this->getUuidName(), $uuid);
    }

    public function getUuidName()
    {
        return property_exists($this, 'uuidName') ? $this->uuidName : 'uuid';
    }

    public static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->{$model->getUuidName()} = Str::orderedUuid()->toString();
        });
    }
}
