<?php

namespace App\Modules\Flickr\Tests\Feature\Jobs;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Tests\TestCase;

class PhotosTest extends TestCase
{
    public function testHandle()
    {
        $pool = Pool::add(Photos::class, ['nsid' => '94529704@N02']);

        $photos = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_photos_1.json'),
            true
        );

        Photos::dispatch($pool);

        $this->assertDatabaseCount('flickr_photos', count($photos['photos']['photo']));
        $this->assertEquals(PoolService::STATE_CODE_COMPLETED, $pool->refresh()->state_code);
    }
}
