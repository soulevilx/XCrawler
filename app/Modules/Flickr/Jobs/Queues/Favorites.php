<?php

namespace App\Modules\Flickr\Jobs\Queues;

/**
 * Fetch user's favorites
 */
class Favorites extends AbstractFlickrQueues
{

    public function process(): bool
    {
        $this->service->favorites()->getList(['user_id' => '22213833@N02']);
dd('dsd');
        return true;
    }
}
