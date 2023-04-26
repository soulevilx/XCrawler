<?php

namespace App\Modules\Flickr\Observer;

use App\Modules\Core\Facades\Pool as PoolFacade;
use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\FoundNewContact;
use App\Modules\Flickr\Jobs\Queues\Favorites;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

class PhotoObserver
{
    public function created(Photo $model)
    {
        $cache = Cache::store('redis');
        $contacts = $cache->rememberForever('flickr_contacts', function () {
            return array_unique(Contact::pluck('nsid')->toArray());
        });

        // If contact doesn't exist, create it
        if (!in_array($model->owner, $contacts)) {
            Event::dispatch(new FoundNewContact($model->owner));
            PoolFacade::add(
                Owner::class,
                [
                    'nsid' => $model->owner,
                ],
                PoolService::QUEUE_API
            );

            $cache->forget('flickr_contacts');
            $cache->rememberForever('flickr_contacts', function () {
                return array_unique(Contact::pluck('nsid')->toArray());
            });
        }

        // If favorites queues of this owner doesn't exist, create it
        if (!Pool::where('ansid', $model->owner)->where('job', Favorites::class)->exists()) {
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
