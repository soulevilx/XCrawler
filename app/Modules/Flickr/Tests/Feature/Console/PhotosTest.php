<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Flickr\Tests\TestCase;

class PhotosTest extends TestCase
{
    public function testPhotos()
    {
        $this->artisan('flickr:photos 123')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_photos', 358, 'mongodb');
    }
}
