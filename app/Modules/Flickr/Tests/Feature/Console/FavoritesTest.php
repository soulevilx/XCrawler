<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Tests\TestCase;

class FavoritesTest extends TestCase
{
    public function testHandle()
    {
        $this->artisan('flickr:favorites 123')
            ->assertExitCode(0);

        /**
         * Invalid number because of json
         */
        $this->assertDatabaseCount('flickr_photos', 1512, 'mongodb');

    }
}
