<?php

namespace App\Modules\Core\Jobs;

use App\Modules\Core\Jobs\Traits\HasLimitted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractApiQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    use HasLimitted;

    protected int $allow = 5;

    public function __construct(public $item)
    {
    }
}
