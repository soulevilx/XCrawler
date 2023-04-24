<?php

namespace App\Modules\Flickr\Observer;

use App\Modules\Core\Models\Pool;
use \App\Modules\Core\Facades\Pool as PoolFacade;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\FoundNewContact;
use App\Modules\Flickr\Jobs\Queues\Favorites;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Support\Facades\Event;

class PhotoObserver
{
    public function created(Photo $model)
    {
        // If contact doesn't exist, create it
        if (!Contact::where('nsid', $model->owner)->exists()) {
            Event::dispatch(new FoundNewContact($model->owner));
            PoolFacade::add(
                Owner::class,
                [
                    'nsid' => $model->owner,
                ],
                PoolService::QUEUE_API
            );
        }

        // If favorites queues of this owner doesn't exist, create it
        if (!Pool::where('payload.nsid', $model->owner)->where('job', Favorites::class)->exists()) {
            // Get user' favorites
            PoolFacade::add(
                Favorites::class,
                [
                    'nsid' => $model->owner,
                ],
                PoolService::QUEUE_API
            );
        }

        // Get sizes
    }

    public function updated(Photo $model)
    {
    }
}
