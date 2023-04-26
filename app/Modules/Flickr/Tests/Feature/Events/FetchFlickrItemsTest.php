<?php

namespace App\Modules\Flickr\Tests\Feature\Events;

use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FetchFlickrItemsTest extends TestCase
{
    public function testWithContacts()
    {
        $contacts = json_decode(
            file_get_contents(__DIR__ . '/../../Fixtures/flickr_contacts_1.json'),
            true
        );

        Contact::create([
            'nsid' => '124830340@N02',
        ]);

        Event::dispatch(
            new FetchedFlickrItems($contacts, 'contacts', 'contact')
        );

        $this->assertDatabaseCount('flickr_contacts', 1000, 'mongodb');
    }

    public function testWithPhotos()
    {
        $photos = json_decode(
            file_get_contents(__DIR__ . '/../../Fixtures/flickr_photos_1.json'),
            true
        );

        Photo::create([
            'owner' => '124284292@N03',
            'id' => '52608033816',
        ]);

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $this->assertEquals(3, Photo::where('owner', '124284292@N03')->count());

        Event::dispatch(
            new FetchedFlickrItems($photos, 'photos', 'photo')
        );

        $this->assertEquals(3, Photo::where('owner', '124284292@N03')->count());
    }
}
