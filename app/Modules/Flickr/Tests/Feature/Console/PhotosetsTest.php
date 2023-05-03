<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Jobs\Queues\PhotosetPhotos;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PhotosetsTest extends TestCase
{
    public function testPhotosets()
    {
        Event::fake([CreatedBulkOfPhotosets::class]);
        $this->artisan('flickr:photosets 94529704@N02')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_photosets', 23);
        Event::assertDispatched(CreatedBulkOfPhotosets::class, function ($event) {
            return count($event->photosets) === 23;
        });

        $this->assertEquals(
            23,
            Pool::where('job', PhotosetPhotos::class)->where('state_code', PoolService::STATE_CODE_INIT)->count()
        );
    }
}
