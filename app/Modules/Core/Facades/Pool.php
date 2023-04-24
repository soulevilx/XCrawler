<?php

namespace App\Modules\Core\Facades;

use App\Modules\Core\Services\Pool\PoolService;
use Illuminate\Support\Facades\Facade;

class Pool extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PoolService::class;
    }
}
