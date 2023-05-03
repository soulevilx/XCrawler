<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Tests\TestCase;
use Illuminate\Support\Facades\Event;

class ContactsTest extends TestCase
{
    public function testExecute()
    {
        Event::fake([CreatedBulkOfContacts::class]);

        $this->artisan('flickr:contacts')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_contacts', 1109);
        $this->assertEquals(
            1109,
            Pool::where('job', Photos::class)->where('state_code', PoolService::STATE_CODE_INIT)->count()
        );
        $this->assertEquals(
            1109,
            Pool::where('job', Photosets::class)->where('state_code', PoolService::STATE_CODE_INIT)->count()
        );

        Event::assertDispatchedTimes(CreatedBulkOfContacts::class, 2);
    }
}
