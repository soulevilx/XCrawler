<?php

namespace App\Modules\Core\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Pool extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'pool';

    protected $guarded = [];

    public function scopeByService(Builder $builder, string $service)
    {
        return $builder->where(compact('service'));
    }
}
