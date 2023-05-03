<?php

namespace App\Modules\Flickr\Tests\Unit\Services;

use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Services\PhotoService;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class PhotoServiceTest extends TestCase
{
    private PhotoService $service;
    private Collection $photos;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(PhotoService::class);
        $this->photos = collect(
            json_decode(
                file_get_contents(__DIR__.'/../../Fixtures/flickr_photos_1.json'),
                true
            )['photos']['photo']
        );
    }

    public function testInsert()
    {
        Event::fake([
            CreatedBulkOfContacts::class,
            CreatedBulkOfPhotos::class,
        ]);

        $this->service->insert($this->photos);
        $this->assertDatabaseCount('flickr_contacts', $this->photos->pluck('owner')->unique()->count());
        $this->assertDatabaseCount('flickr_photos', $this->photos->count());

        Event::assertDispatched(CreatedBulkOfContacts::class, function ($e) {
            return count($e->nsids) === $this->photos->pluck('owner')->unique()->count();
        });

        Event::assertDispatched(CreatedBulkOfPhotos::class, function ($e) {
            return count($e->photos) === $this->photos->count();
        });
    }

    public function testInsertWithExistPhotos()
    {
        Event::fake([
            CreatedBulkOfContacts::class,
            CreatedBulkOfPhotos::class,
        ]);

        Contact::factory()->create([
            'nsid' => $this->photos->first()['owner'],
        ]);
        Photo::factory()->create($this->photos->first());

        $this->service->insert($this->photos);

        $this->assertEquals($this->photos->count(), Photo::count());

        Event::assertNotDispatched(CreatedBulkOfContacts::class);
        Event::assertDispatched(CreatedBulkOfPhotos::class, function ($e) {
            return count($e->photos) === $this->photos->count() - 1;
        });
    }
}
