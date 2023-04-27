<?php

namespace App\Modules\Flickr\Jobs;

use App\Modules\Core\Jobs\AbstractApiQueue;
use App\Modules\Flickr\Services\FlickrService;

class Photos extends AbstractApiQueue
{
    public FlickrService $service;

    public function __construct(
        public string $nsid,
    ) {
    }

    public function handle(): int
    {
        app(FlickrService::class)
            ->people()
            ->getList(['user_id' => $this->nsid]);

        return 0;
    }
}
