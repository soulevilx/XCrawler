<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

abstract class AbstractFlickrQueues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public FlickrService $service;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;


    public function __construct(public $model)
    {
        $this->service = app(FlickrService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->model) {
            return 0;
        }

        if ($this->process()) {
            $this->model->delete();
        }

        return 0;
    }

    abstract public function process(): bool;
}
