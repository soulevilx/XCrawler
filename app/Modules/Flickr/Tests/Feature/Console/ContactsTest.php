<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\Models\Queue;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    public function testCreatedObserver()
    {
        Queue::truncate();
        Photo::truncate();
        Contact::truncate();

        $contact = Contact::create([
            'nsid' => $this->faker->uuid,
        ]);
        Contact::create([
            'nsid' => $contact->nsid,
        ]);

        $this->assertDatabaseHas('queues', [
            'job' => Photos::class,
        ], 'mongodb');

        $queues = Queue::where('payload.nsid', $contact->nsid)->where('job', Photos::class)->get();

        $this->assertEquals(1, $queues->count());

        Photo::create([
            'id' => $this->faker->uuid,
            'owner' => $contact->nsid,
        ]);

        // This contact already exists
        $this->assertFalse(Queue::where('payload.nsid', $contact->nsid)->where('job', Owner::class)->exists());

        $photo = Photo::create([
            'id' => $this->faker->uuid,
            'owner' => $this->faker->uuid,
        ]);

        // This contact doesn't exist
        $this->assertTrue(Queue::where('payload.nsid', $photo->owner)->where('job', Owner::class)->exists());
    }
}
