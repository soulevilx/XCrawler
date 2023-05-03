<?php

namespace App\Modules\Flickr\Tests\Unit\Services;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Services\ContactService;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ContactServiceTest extends TestCase
{
    private ContactService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(ContactService::class);
    }

    public function testInsert()
    {
        Event::fake([CreatedBulkOfContacts::class]);
        $contacts = collect(
            json_decode(
                file_get_contents(__DIR__.'/../../Fixtures/flickr_contacts_1.json'),
                true
            )['contacts']['contact']
        );

        $this->service->insert($contacts);

        $this->assertDatabaseCount('flickr_contacts', $contacts->count());
        $this->assertEquals($contacts->count(), Pool::where('job', Photos::class)->count());
        $this->assertEquals($contacts->count(), Pool::where('job', Photosets::class)->count());

        Event::assertDispatched(CreatedBulkOfContacts::class, function ($e) use ($contacts) {
            return count($e->nsids) === $contacts->count();
        });
    }

    public function testInsertExistContact()
    {
        Event::fake([CreatedBulkOfContacts::class]);

        $contact = Contact::factory()->create();

        $this->service->insert(collect([$contact->toArray()]));

        $this->assertDatabaseCount('flickr_contacts', 1);
        $this->assertEquals(
            1,
            Pool::where('job', Photos::class)->count()
        );
        $this->assertEquals(
            1,
            Pool::where('job', Photosets::class)->count()
        );

        Event::assertNotDispatched(CreatedBulkOfContacts::class);
    }

    public function testInsertExistContactWithPhotosPool()
    {
        $contact = Contact::factory()->create();
        Pool::create([
            'job' => Photos::class,
            'nsid' => $contact->nsid,
            'state_code' => PoolService::STATE_CODE_INIT,
        ]);

        $this->service->insert(collect([$contact->toArray()]));

        $this->assertDatabaseCount('flickr_contacts', 1);
        $this->assertEquals(
            1,
            Pool::where('job', Photos::class)->count()
        );
        $this->assertEquals(
            1,
            Pool::where('job', Photosets::class)->count()
        );
    }

    public function testInsertExistContactWithPhotosetsPool()
    {
        $contact = Contact::factory()->create();
        Pool::create([
            'job' => Photosets::class,
            'nsid' => $contact->nsid,
            'state_code' => PoolService::STATE_CODE_INIT,
        ]);

        $this->service->insert(collect([$contact->toArray()]));

        $this->assertDatabaseCount('flickr_contacts', 1);
        $this->assertEquals(
            1,
            Pool::where('job', Photos::class)->count()
        );
        $this->assertEquals(
            1,
            Pool::where('job', Photosets::class)->count()
        );
    }
}
