<?php

namespace App\Modules\Core\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Queue extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'queues';

    protected $guarded = [];

    public const STATE_CODE_INIT = 'INIT';
    public const STATE_CODE_PROCESSING = 'PROCESSING';

    public function scopeByService(Builder $builder, string $service)
    {
        return $builder->where(compact('service'));
    }
}
