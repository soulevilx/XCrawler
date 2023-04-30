<?php

namespace App\Modules\Flickr\Jobs\Queues;

class PhotosetPhotos extends AbstractFlickrQueues
{
    public function process(): bool
    {
        $this->service
            ->photosets()
            ->getPhotos(['photoset_id' => $this->item->id]);

        return 0;
    }
}
