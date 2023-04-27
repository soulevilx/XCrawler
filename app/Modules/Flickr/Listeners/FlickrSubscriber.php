<?php

namespace App\Modules\Flickr\Listeners;

use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\People;
use App\Modules\Flickr\Services\Adapters\PhotoSets;
use Illuminate\Events\Dispatcher;

class FlickrSubscriber
{
    public function fetchedFlickrItems(FetchedFlickrItems $event): void
    {
        $items = collect($event->data[$event->listEntities][$event->listEntity]);

        switch ($event->listEntities) {
            case Contacts::LIST_ENTITIES:
                $existsContacts = Contact::whereIn('nsid', $items->pluck('nsid'))->pluck('nsid')->toArray();
                $items = $items->filter(
                    fn($item) => !in_array($item['nsid'], $existsContacts)
                )->values()->all();

                foreach ($items as $item) {
                    Contact::create($item);
                }

                break;
            case People::LIST_ENTITIES:
                $items = $items->groupBy('owner');

                foreach ($items as $owner => $photos) {
                    $existsPhotos = Photo::where('owner', $owner)
                        ->whereIn('id', $photos->pluck('id')->toArray())
                        ->pluck('id')->toArray();
                    $items = $photos->filter(
                        fn($item) => !in_array($item['id'], $existsPhotos)
                    )->values()->all();

                    foreach ($items as $item) {
                        Photo::create($item);
                    }
                }
                break;
            case PhotoSets::LIST_ENTITIES:
                if ($event->listEntity === PhotoSets::LIST_ENTITY) {

                    $existsPhotosets = Photoset::whereIn('id', $items->pluck('id'))
                        ->where('owner', $event->params['user_id'])
                        ->pluck('id')->toArray();
                    $items = $items->filter(
                        fn($item) => !in_array($item['id'], $existsPhotosets)
                    )->values()->all();

                    foreach ($items as $item) {
                        Photoset::create($item);
                    }
                }

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
