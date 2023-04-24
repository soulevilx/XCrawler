<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Core\Facades\Pool;
use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractFlickrQueues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public FlickrService $service;

    public function __construct(public $model)
    {
        $this->service = app(FlickrService::class);
    }

    public function handle(): int
    {
        if (!$this->model) {
            return 0;
        }

        if ($this->process()) {
            Pool::complete($this->model);
        }

        return 0;
    }

    abstract public function process(): bool;
}
