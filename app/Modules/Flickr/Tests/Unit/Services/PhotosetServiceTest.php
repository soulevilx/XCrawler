<?php

namespace App\Modules\Flickr\Tests\Unit\Services;

use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Services\PhotosetService;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class PhotosetServiceTest extends TestCase
{
    private PhotosetService $service;
    private Collection $photosets;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(PhotosetService::class);
        $this->photosets = collect(
            json_decode(
                file_get_contents(__DIR__.'/../../Fixtures/flickr_photosets.json'),
                true
            )['photosets']['photoset']
        );
        $this->photosetPhotos =
            json_decode(
                file_get_contents(__DIR__.'/../../Fixtures/flickr_photoset_photos.json'),
                true
            );
    }

    public function testInsert()
    {
        Event::fake([
            CreatedBulkOfPhotosets::class,
        ]);

        $this->service->insert($this->photosets);
        $this->assertDatabaseCount('flickr_contacts', $this->photosets->pluck('owner')->unique()->count());
        $this->assertDatabaseCount('flickr_photosets', $this->photosets->count());

        Event::assertDispatched(CreatedBulkOfPhotosets::class, function ($e) {
            return count($e->photosets) === $this->photosets->count();
        });
    }

    public function testInsertPhotosetPhotos()
    {
        Event::fake([
            CreatedBulkOfPhotos::class,
        ]);

        $this->service->insertPhotos($this->photosetPhotos);
        $photoset = Photoset::find('72157692062490774')->first();

        $this->assertEquals(count($this->photosetPhotos['photoset']['photo']), $photoset->photos->count());

        Event::assertDispatched(CreatedBulkOfPhotos::class, function ($e) {
            return count($e->photos) === count($this->photosetPhotos['photoset']['photo']);
        });
    }
}
