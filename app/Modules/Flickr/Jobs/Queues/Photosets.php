<?php

namespace App\Modules\Flickr\Jobs\Queues;

class Photosets extends AbstractFlickrQueues
{
    public function process(): bool
    {
        $this->service
            ->photosets()
            ->getList(['user_id' => $this->item->nsid]);

        return true;
    }
}
