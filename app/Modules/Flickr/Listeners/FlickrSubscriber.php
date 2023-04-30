<?php

namespace App\Modules\Flickr\Listeners;

use App\Modules\Core\Models\Download;
use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\People;
use App\Modules\Flickr\Services\Adapters\PhotoSets;
use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Events\Dispatcher;

class FlickrSubscriber
{
    public function fetchedFlickrItems(FetchedFlickrItems $event): void
    {
        $items = collect($event->data[$event->listEntities][$event->listEntity]);

        switch ($event->listEntities) {
            case Contacts::LIST_ENTITIES:
                app(FlickrService::class)->contacts()->createMany($items);

                break;
            case People::LIST_ENTITIES:
                app(FlickrService::class)->people()->createMany($items);

                break;
            case PhotoSets::LIST_ENTITIES:
                app(FlickrService::class)->photosets()->createMany($items);

                break;
        }
    }

    public function createdBulkOfPhotosets(CreatedBulkOfPhotosets $event): void
    {
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            FetchedFlickrItems::class,
            [self::class, 'fetchedFlickrItems']
        );

        $events->listen(
            CreatedBulkOfPhotosets::class,
            [self::class, 'createdBulkOfPhotosets']
        );
    }
}
