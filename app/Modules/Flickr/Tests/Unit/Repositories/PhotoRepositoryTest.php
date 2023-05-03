<?php

namespace App\Modules\Flickr\Tests\Unit\Repositories;

use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Repositories\PhotoRepository;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PhotoRepositoryTest extends TestCase
{
    public function testInsert()
    {
        Event::fake([CreatedBulkOfPhotos::class]);
        $repository = app(PhotoRepository::class);

        $items = collect([
            [
                'uuid' => $this->faker->uuid,
                'id' => $this->faker->randomNumber(),
                'owner' => $this->faker->uuid,
                'secret' => $this->faker->uuid,
                'server' => $this->faker->randomNumber(),
                'farm' => $this->faker->randomNumber(),
            ],
            [
                'uuid' => $this->faker->uuid,
                'id' => $this->faker->randomNumber(),
                'owner' => $this->faker->uuid,
                'secret' => $this->faker->uuid,
                'server' => $this->faker->randomNumber(),
                'farm' => $this->faker->randomNumber(),
            ],
        ]);

        $this->assertTrue($repository->insert($items));
        $this->assertDatabaseCount('flickr_photos', 2);
        $this->assertDatabaseCount('flickr_contacts', 2);

        Event::assertDispatchedTimes(CreatedBulkOfPhotos::class, 2);
    }
}
