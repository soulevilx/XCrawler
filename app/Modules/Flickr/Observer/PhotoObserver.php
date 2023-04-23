<?php

namespace App\Modules\Flickr\Observer;

use App\Modules\Core\Models\Queue;
use App\Modules\Flickr\Jobs\Queues\Favorites;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;

class PhotoObserver
{
    public function created(Photo $model)
    {
        // If contact doesn't exist, create it
        if (!Contact::where('nsid', $model->owner)->exists()) {
            Queue::create([
                'queue' => 'low',
                'state_code' => Queue::STATE_CODE_INIT,
                'job' => Owner::class,
                'payload' => [
                    'nsid' => $model->owner,
                ],
            ]);
        }

        // If favorites queues of this owner doesn't exist, create it
        if (!Queue::where('payload.nsid', $model->owner)->where('job', Favorites::class)->exists()) {
            // Get user' favorites
            Queue::create([
                'queue' => 'low',
                'state_code' => Queue::STATE_CODE_INIT,
                'job' => Favorites::class,
                'payload' => [
                    'nsid' => $model->owner,
                ],
            ]);
        }

        // Get sizes
    }

    public function updated(Photo $model)
    {
    }
}
