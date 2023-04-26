<?php

namespace App\Modules\Flickr\Tests\Feature\Observers;

use App\Modules\Core\Models\Pool;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class PhotoObserverTest extends TestCase
{
    public function testCreatedObserver()
    {
        Pool::truncate();
        Photo::truncate();
        Contact::truncate();

        $cache = Cache::store('redis');

        $contact = Contact::create(['nsid' => $this->faker->uuid]);
        Contact::create(['nsid' => $contact->nsid]);

        $this->assertDatabaseHas('pool', ['job' => Photos::class], 'mongodb');
        $this->assertEquals(1, Pool::where('nsid', $contact->nsid)->where('job', Photos::class)->count());
        $this->assertNull($cache->get('flickr_contacts'));

        /**
         * First time observer is called, cache is not here
         */
        Photo::create(['id' => $this->faker->uuid, 'owner' => $contact->nsid]);

        $this->assertEquals([
            $contact->nsid,
        ], Cache::store('redis')->get('flickr_contacts'));

        // This contact already exists
        $this->assertFalse(Pool::where('nsid', $contact->nsid)->where('job', Owner::class)->exists());

        /**
         * Second time observer is called, cache is here
         */
        $photo = Photo::create(['id' => $this->faker->uuid, 'owner' => $this->faker->uuid]);

        // This contact doesn't exist
        $this->assertTrue(Pool::where('nsid', $photo->owner)->where('job', Owner::class)->exists());
    }
}
