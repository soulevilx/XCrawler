<?php

namespace App\Modules\Flickr\Tests\Unit\Adapters;

use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\FlickrService;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ContactsTest extends TestCase
{
    public function testGetList()
    {
        Event::fake(FetchedFlickrItems::class);

        app(FlickrService::class)->contacts()->getList();

        Event::assertDispatchedTimes(FetchedFlickrItems::class, 2);
        Event::assertDispatched(FetchedFlickrItems::class, static function ($event) {
            return $event->data['contacts']['page'] === 1
                && $event->data['contacts']['pages'] === 2
                && $event->data['contacts']['per_page'] == 1000
                && $event->data['contacts']['total'] == 1109;
        });
    }
}
