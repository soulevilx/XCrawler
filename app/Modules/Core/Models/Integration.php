<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Integration extends Model implements BaseModelInterface
{
    protected $connection = 'mongodb';

    protected $collection = 'integrations';

    protected $guarded = [];

    public function scopeByService(Builder $builder, string $service)
    {
        return $builder->where(compact('service'));
    }
}
