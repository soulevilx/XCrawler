<?php

namespace App\Modules\Flickr\Tests\Feature\Events;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class FetchFlickrItemsTest extends TestCase
{
    public function testWithContacts()
    {
        Event::fake([CreatedBulkOfContacts::class]);
        $contacts = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_contacts_1.json'),
            true
        );

        $nsid = '124830340@N02';
        Contact::create(compact('nsid'));

        Pool::add(
            Photos::class,
            compact('nsid'),
            PoolService::QUEUE_API
        );

        Event::dispatch(
            new FetchedFlickrItems($contacts, 'contacts', 'contact')
        );

        $this->assertDatabaseCount('flickr_contacts', 1000, 'mongodb');
        $this->assertEquals(
            1000,
            \App\Modules\Core\Models\Pool::where('job', Photos::class)->where(
                'state_code',
                PoolService::STATE_CODE_INIT
            )->count()
        );

        Event::assertDispatched(CreatedBulkOfContacts::class, static function ($event) {
            return count($event->nsid) === 999;
        });
    }

    public function testWithPhotos()
    {
        $photos = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_favorites_1.json'),
            true
        );

        Photo::create([
            'owner' => '124284292@N03',
            'id' => '52608033816',
        ]);

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $countPhotosByOwner = collect($photos['photos']['photo'])->groupBy('owner')['124284292@N03']->count();

        $this->assertEquals(
            $countPhotosByOwner,
            Photo::where('owner', '124284292@N03')->count()
        );

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $this->assertEquals($countPhotosByOwner, Photo::where('owner', '124284292@N03')->count());
        $this->assertDatabaseCount('flickr_photos', count($photos['photos']['photo']), 'mongodb');
    }
}
