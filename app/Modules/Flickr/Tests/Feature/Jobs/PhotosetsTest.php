<?php

namespace App\Modules\Flickr\Tests\Feature\Jobs;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Tests\TestCase;

class PhotosetsTest extends TestCase
{
    public function testHandle()
    {
        $pool = Pool::add(Photosets::class, ['nsid' => '94529704@N02']);

        $photosets = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_photosets.json'),
            true
        );

        Photosets::dispatch($pool);

        $this->assertDatabaseCount('flickr_photosets', count($photosets['photosets']['photoset']));
        $this->assertEquals(PoolService::STATE_CODE_COMPLETED, $pool->refresh()->state_code);
    }
}
