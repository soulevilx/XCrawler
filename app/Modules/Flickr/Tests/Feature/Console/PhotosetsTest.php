<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Flickr\Tests\TestCase;

class PhotosetsTest extends TestCase
{
    public function testPhotos()
    {
        $this->artisan('flickr:photosets 123')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_photosets', 23, 'mongodb');
    }
}
