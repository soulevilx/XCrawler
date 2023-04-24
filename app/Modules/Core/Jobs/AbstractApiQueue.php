<?php

namespace App\Modules\Core\Jobs;

use App\Modules\Core\Services\Pool\PoolService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class AbstractApiQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct()
    {
        $this->onQueue(PoolService::QUEUE_API);
    }
}
