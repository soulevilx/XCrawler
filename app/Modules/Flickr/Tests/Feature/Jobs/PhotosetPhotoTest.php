<?php

namespace App\Modules\Flickr\Tests\Feature\Jobs;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\PhotosetPhotos;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Tests\TestCase;

class PhotosetPhotoTest extends TestCase
{
    public function testHandle()
    {
        $pool = Pool::add(PhotosetPhotos::class, ['id' => '72157692062490774']);

        $photos = json_decode(
            file_get_contents(__DIR__.'/../../Fixtures/flickr_photoset_photos.json'),
            true
        );

        PhotosetPhotos::dispatch($pool);

        $this->assertDatabaseCount('flickr_photos', count($photos['photoset']['photo']));
        $this->assertEquals(PoolService::STATE_CODE_COMPLETED, $pool->refresh()->state_code);

        $photoset = Photoset::where('id', '72157692062490774')->first();
        $this->assertEquals($photoset->photos()->count(), count($photos['photoset']['photo']));
    }
}
