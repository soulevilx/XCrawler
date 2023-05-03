<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PhotosTest extends TestCase
{
    public function testPhotos()
    {
        Event::fake([CreatedBulkOfPhotos::class]);
        $this->artisan('flickr:photos 94529704@N02')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_photos', 358);

        $contact = Contact::byNsid('94529704@N02')->first();
        $this->assertEquals(358, $contact->photos()->count());
        $contact->photos()->each(function (Photo $photo) {
            $this->assertDatabaseHas('flickr_photos', [
                'id' => $photo->id,
                'owner' => '94529704@N02',
            ]);
        });

        Event::assertDispatched(CreatedBulkOfPhotos::class, function ($event) {
            return count($event->photos) === 358;
        });
    }
}
