<?php

namespace App\Modules\Flickr\Tests\Feature\Jobs;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Tests\TestCase;

class OwnerTest extends TestCase
{
    public function testHandle()
    {
        $pool = Pool::add(Owner::class, ['nsid' => '16842686@N04']);
        $contact = Contact::factory()->create(['nsid' => '16842686@N04']);

        Owner::dispatch($pool);

        $contact->refresh();

        $this->assertTrue($contact->ispro);
        $this->assertFalse($contact->is_deleted);
        $this->assertEquals(PoolService::STATE_CODE_COMPLETED, $pool->refresh()->state_code);
    }
}
