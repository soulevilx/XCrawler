<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Pool extends Model implements BaseModelInterface
{
    protected $connection = 'mongodb';

    protected $collection = 'pool';

    protected $guarded = [];

    public function scopeByJob(Builder $builder, string $job)
    {
        return $builder->where(compact('job'));
    }
}
