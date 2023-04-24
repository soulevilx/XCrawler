<?php

namespace App\Modules\Core\Jobs;

use App\Modules\Core\Jobs\Traits\HasLimitted;
use App\Modules\Core\Services\Pool\PoolService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class AbstractLowQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
    use HasLimitted;

    public function __construct()
    {
        $this->onQueue(PoolService::QUEUE_LOW);
    }
}
