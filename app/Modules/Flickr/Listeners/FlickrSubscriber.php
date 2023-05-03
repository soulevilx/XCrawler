<?php

namespace App\Modules\Flickr\Listeners;

use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\People;
use App\Modules\Flickr\Services\Adapters\PhotoSets;
use App\Modules\Flickr\Services\ContactService;
use App\Modules\Flickr\Services\PhotoService;
use App\Modules\Flickr\Services\PhotosetService;
use Illuminate\Events\Dispatcher;

class FlickrSubscriber
{
    public function fetchedFlickrItems(FetchedFlickrItems $event): void
    {
        $items = collect($event->data[$event->listEntities][$event->listEntity]);

        switch ($event->listEntities) {
            case Contacts::LIST_ENTITIES:
                app(ContactService::class)->insert($items);

                break;
            case People::LIST_ENTITIES:
                app(PhotoService::class)->insert($items);

                break;
            case PhotoSets::LIST_ENTITIES:
                app(PhotosetService::class)->insert($items);

                break;
            case PhotoSets::LIST_ENTITY:
                app(PhotosetService::class)->insertPhotos($event->data);

                break;
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
