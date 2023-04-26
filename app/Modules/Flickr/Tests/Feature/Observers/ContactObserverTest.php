<?php

namespace App\Modules\Flickr\Tests\Feature\Observers;

use App\Modules\Core\Models\Pool;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Tests\TestCase;

class ContactObserverTest extends TestCase
{
    public function testCreatedObserver()
    {
        Pool::truncate();
        Photo::truncate();
        Contact::truncate();

        $contact = Contact::create(['nsid' => $this->faker->uuid]);
        Contact::create(['nsid' => $contact->nsid]);

        $this->assertDatabaseHas('pool', ['job' => Photos::class], 'mongodb');
        $this->assertEquals(1, Pool::where('nsid', $contact->nsid)->where('job', Photos::class)->count());

        Photo::create(['id' => $this->faker->uuid, 'owner' => $contact->nsid]);

        // This contact already exists
        $this->assertFalse(Pool::where('nsid', $contact->nsid)->where('job', Owner::class)->exists());

        $photo = Photo::create(['id' => $this->faker->uuid, 'owner' => $this->faker->uuid]);

        // This contact doesn't exist
        $this->assertTrue(Pool::where('nsid', $photo->owner)->where('job', Owner::class)->exists());
    }
}
