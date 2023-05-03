<?php

namespace App\Modules\Flickr\Tests\Feature\Jobs;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Jobs\Queues\Favorites;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class FavoritesTest extends TestCase
{
    public function testHandle()
    {
        Event::fake(CreatedBulkOfPhotos::class);
        $pool = Pool::add(Favorites::class, ['nsid' => '123']);

        Favorites::dispatch($pool);

        $this->assertDatabaseCount('flickr_photos', 1512);
        $this->assertDatabaseCount(
            'flickr_contacts',
            Photo::groupBy('owner')->get()->pluck('owner')->unique()->count()
        );
        Event::assertDispatched(CreatedBulkOfPhotos::class);
        $this->assertEquals(PoolService::STATE_CODE_COMPLETED, $pool->refresh()->state_code);
    }
}
