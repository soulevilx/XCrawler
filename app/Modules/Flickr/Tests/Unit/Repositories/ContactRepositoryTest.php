<?php

namespace App\Modules\Flickr\Tests\Unit\Repositories;

use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Repositories\ContactRepository;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ContactRepositoryTest extends TestCase
{
    public function testInsert()
    {
        Event::fake([CreatedBulkOfContacts::class]);
        $repository = app(ContactRepository::class);

        $items = collect([
            [
                'nsid' => $this->faker->uuid,
                'username' => $this->faker->userName,
                'iconserver' => $this->faker->uuid,
                'iconfarm' => $this->faker->uuid,
                'ignored' => $this->faker->boolean,
                'rev_ignored' => $this->faker->boolean,
                'realname' => $this->faker->name,
                'friend' => $this->faker->boolean,
                'family' => $this->faker->boolean,
                'path_alias' => $this->faker->userName,
                'location' => $this->faker->city,
            ],
            [
                'nsid' => $this->faker->uuid,
                'username' => $this->faker->userName,
                'iconserver' => $this->faker->uuid,
                'iconfarm' => $this->faker->uuid,
                'ignored' => $this->faker->boolean,
                'rev_ignored' => $this->faker->boolean,
                'realname' => $this->faker->name,
                'friend' => $this->faker->boolean,
                'family' => $this->faker->boolean,
                'path_alias' => $this->faker->userName,
                'location' => $this->faker->city,
            ],
        ]);

        $this->assertTrue($repository->insert($items));
        $this->assertDatabaseCount('flickr_contacts', 2);

        Event::assertDispatched(CreatedBulkOfContacts::class, function ($e) use ($items) {
            return $e->nsids === $items->pluck('nsid')->toArray();
        });

        // All items are duplicated
        $this->assertFalse($repository->insert($items));
    }
}
