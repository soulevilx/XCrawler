<?php

namespace App\Modules\Flickr\Tests\Feature\Events;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
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
        $this->assertEquals(
            1000,
            \App\Modules\Core\Models\Pool::where('job', Photosets::class)->where(
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
        $nsid = '124284292@N03';

        $photos = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_favorites_1.json'),
            true
        );

        Photo::create([
            'owner' => $nsid,
            'id' => '52608033816',
        ]);

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $countPhotosByOwner = collect($photos['photos']['photo'])->groupBy('owner')[$nsid]->count();

        $this->assertEquals(
            $countPhotosByOwner,
            Photo::where('owner', $nsid)->count()
        );

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $this->assertEquals($countPhotosByOwner, Photo::where('owner', $nsid)->count());
        /**
         * We need fetch this owner only 1 time
         */
        $this->assertEquals(collect($photos['photos']['photo'])->groupBy('owner')->keys()->count(), \App\Modules\Core\Models\Pool::where('job', Owner::class)->count());
        $this->assertDatabaseCount('flickr_photos', count($photos['photos']['photo']), 'mongodb');
    }

    public function testWithPhotosets()
    {
        $photosẹts = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_photosets.json'),
            true
        );

        $countPhotosets = collect($photosẹts['photosets']['photoset'])->count();


        Event::dispatch(
            new FetchedFlickrItems($photosẹts, 'photosets', 'photoset')
        );

        $this->assertEquals($countPhotosets, Photoset::count());
    }

    public function testWithPhotosetPhotos()
    {
        $photosẹts = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_photoset_photos.json'),
            true
        );

        $countPhotosetPhotos = collect($photosẹts['photoset']['photo'])->count();

        Event::dispatch(
            new FetchedFlickrItems($photosẹts, 'photoset', 'photo')
        );

        $this->assertEquals($countPhotosetPhotos, Photo::count());
        $photoset = Photoset::where('id', $photosẹts['photoset']['id'])->first();
        $this->assertEquals($countPhotosetPhotos, $photoset->photos->count());
    }
}
