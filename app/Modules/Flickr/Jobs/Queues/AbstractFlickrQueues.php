<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Jobs\AbstractApiQueue;
use App\Modules\Flickr\Services\FlickrService;

abstract class AbstractFlickrQueues extends AbstractApiQueue
{
    public FlickrService $service;

    public function handle(): int
    {
        $this->service = app(FlickrService::class);

        if (!$this->item) {
            return 0;
        }

        if ($this->process()) {
            Pool::complete($this->item);
        }

        return 0;
    }

    abstract public function process(): bool;
}
