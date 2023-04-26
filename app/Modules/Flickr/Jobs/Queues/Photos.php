<?php

namespace App\Modules\Flickr\Jobs\Queues;

class Photos extends AbstractFlickrQueues
{
    public function process(): bool
    {
        $this->service
            ->people()
            ->getList(['user_id' => $this->item->nsid]);

        return true;
    }
}
