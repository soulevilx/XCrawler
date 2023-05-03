<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class FavoritesTest extends TestCase
{
    public function testGetFavorites()
    {
        Event::fake([CreatedBulkOfPhotos::class]);
        $this->artisan('flickr:favorites 123')
            ->assertExitCode(0);

        /**
         * Invalid number because of json
         */
        $this->assertDatabaseCount('flickr_photos', 1512);
        Event::assertDispatched(CreatedBulkOfPhotos::class);
    }
}
