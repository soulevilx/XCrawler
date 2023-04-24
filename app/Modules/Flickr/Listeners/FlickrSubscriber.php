<?php

namespace App\Modules\Flickr\Listeners;

use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\People;
use Illuminate\Events\Dispatcher;

class FlickrSubscriber
{
    public function fetchedFlickrItems(FetchedFlickrItems $event): void
    {
        foreach ($event->data[$event->listEntities][$event->listEntity] as $item) {
            switch ($event->listEntities) {
                case  Contacts::LIST_ENTITIES:
                    Contact::updateOrCreate(['nsid' => $item['nsid']], $item);
                    break;
                case People::LIST_ENTITIES:
                    Photo::updateOrCreate(
                        [
                            'id' => $item['id'],
                            'owner' => $item['owner'],
                        ],
                        $item
                    );
                    break;
            }
        }
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            FetchedFlickrItems::class,
            [self::class, 'fetchedFlickrItems']
        );
    }
}
