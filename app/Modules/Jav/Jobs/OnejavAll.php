<?php

namespace App\Modules\Jav\Jobs;

use App\Modules\Core\Jobs\Traits\HasLimitted;
use App\Modules\Jav\Services\OnejavService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OnejavAll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasLimitted;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $service = app(OnejavService::class);
        $service->all();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
