<?php

namespace App\Modules\Core\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Pool extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'pool';

    protected $guarded = [];

    public function scopeByJob(Builder $builder, string $job)
    {
        return $builder->where(compact('job'));
    }
}
