<?php

namespace App\Modules\Flickr\Jobs\Queues;

class Photos extends AbstractFlickrQueues
{
    public function process(): bool
    {
        $this->service
            ->people()
            ->getList(['user_id' => $this->model->payload['nsid']]);

        return true;
    }
}
