<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Flickr\Models\Photo;

/**
 * Fetch user's favorites
 */
class Favorites extends AbstractFlickrQueues
{

    public function process(): bool
    {
        $photos = $this->service
            ->favorites()
            ->getList(['user_id' => $this->model->payload['nsid']]);

        foreach ($photos as $photo) {
            Photo::updateOrCreate(
                [
                    'id' => $photo['id'],
                    'owner' => $photo['owner'],
                ],
                $photo
            );
        }

        return true;
    }
}
