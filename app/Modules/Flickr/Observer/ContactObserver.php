<?php

namespace App\Modules\Flickr\Observer;

use App\Modules\Core\Models\Queue;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;

class ContactObserver
{
    public function created(Contact $model)
    {
        // Get user's photos
        if (!Queue::where('payload.nsid', $model->nsid)->where('job', Photos::class)->exists()) {
            Queue::create([
                'queue' => 'api',
                'job' => Photos::class,
                'payload' => [
                    'nsid' => $model->nsid,
                ],
            ]);
        }

        // Get public list
    }

    public function updated(Contact $model)
    {
    }
}
