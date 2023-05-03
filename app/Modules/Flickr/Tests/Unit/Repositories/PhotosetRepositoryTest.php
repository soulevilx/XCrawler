<?php

namespace App\Modules\Flickr\Tests\Unit\Repositories;

use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Repositories\PhotosetRepository;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class PhotosetRepositoryTest extends TestCase
{
    public function testInsert()
    {
        Event::fake([CreatedBulkOfPhotosets::class]);
        $repository = app(PhotosetRepository::class);

        $items = collect([
            [
                'id' => $this->faker->randomNumber(),
                'owner' => $this->faker->uuid,
                'secret' => $this->faker->uuid,
                'server' => $this->faker->uuid,
                'farm' => $this->faker->uuid,
            ],
            [
                'id' => $this->faker->randomNumber(),
                'owner' => $this->faker->uuid,
                'secret' => $this->faker->uuid,
                'server' => $this->faker->uuid,
                'farm' => $this->faker->uuid,
            ],
        ]);

        $this->assertTrue($repository->insert($items));
        $this->assertDatabaseCount('flickr_photosets', 2);
        $this->assertDatabaseCount('flickr_contacts', 2);

        Event::assertDispatched(CreatedBulkOfPhotosets::class, function ($e) use ($items) {
            return $e->photosets === $items->pluck('id')->toArray();
        });
    }
}
