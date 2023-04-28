<?php

namespace App\Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractApiQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected int $allow = 5;
}
