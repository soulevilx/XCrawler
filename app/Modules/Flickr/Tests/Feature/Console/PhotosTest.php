<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\Facades\Pool;
use App\Modules\Flickr\Jobs\Queues\Photos;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PhotosTest extends TestCase
{
    public function testPhotos()
    {
        \App\Modules\Core\Models\Pool::truncate();
        Queue::fake(Photos::class);
        Pool::add(Photos::class, ['nsid' => '123']);

        $this->artisan('flickr:queues-photos')
            ->assertExitCode(0);

        Queue::assertPushed(Photos::class, function ($job) {
            return $job->item['nsid'] === '123';
        });

        $this->assertDatabaseMissing('pools', [
            'job' => Photos::class,
            'nsid' =>'123'
        ], 'mongodb');

    }
}
