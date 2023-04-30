<?php

namespace App\Modules\Flickr\Listeners;

use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\People;
use App\Modules\Flickr\Services\Adapters\PhotoSets;
use App\Modules\Flickr\Services\ContactsService;
use App\Modules\Flickr\Services\PhotosetsService;
use App\Modules\Flickr\Services\PhotosService;
use Illuminate\Events\Dispatcher;

class FlickrSubscriber
{
    public function fetchedFlickrItems(FetchedFlickrItems $event): void
    {
        $items = collect($event->data[$event->listEntities][$event->listEntity]);

        switch ($event->listEntities) {
            case Contacts::LIST_ENTITIES:
                app(ContactsService::class)->insertBulk($items);

                break;
            case People::LIST_ENTITIES:
                app(PhotosService::class)->insertBulk($items);

                break;
            case PhotoSets::LIST_ENTITIES:
                app(PhotosetsService::class)->insertPhotosetsBulk($items);

                break;
            case PhotoSets::LIST_ENTITY:
                app(PhotosetsService::class)->createPhotosBulk($event->data);

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
