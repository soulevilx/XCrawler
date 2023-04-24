<?php

namespace App\Modules\Flickr\Jobs\Queues;

/**
 * Fetch user's favorites
 */
class Favorites extends AbstractFlickrQueues
{

    public function process(): bool
    {
        $this->service->favorites()->getList(['user_id' => $this->model->payload['nsid']]);

        return true;
    }
}
