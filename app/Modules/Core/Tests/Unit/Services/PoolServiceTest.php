<?php

namespace App\Modules\Core\Tests\Unit\Services;

use App\Modules\Core\Events\PoolItemAdded;
use App\Modules\Core\Events\PoolItemCompleted;
use App\Modules\Core\Events\PoolItemRemoved;
use App\Modules\Core\Services\Pool\PoolService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PoolServiceTest extends TestCase
{
    private PoolService $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = app(PoolService::class);
    }

    public function testAdd(): void
    {
        Event::fake(PoolItemAdded::class);
        $this->service->add('test', ['payload']);

        $this->assertDatabaseHas('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_INIT,
            'job' => 'test',
            'payload' => ['payload']
        ], 'mongodb');

        Event::assertDispatched(PoolItemAdded::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->payload === ['payload']
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_INIT;
        });
    }

    public function testPoolDeleted(): void
    {
        Event::fake(PoolItemRemoved::class);
        $pool = $this->service->add('test', ['payload']);
        $this->service->remove($pool);

        $this->assertDatabaseMissing('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_INIT,
            'job' => 'test',
            'payload' => ['payload']
        ], 'mongodb');

        Event::assertDispatched(PoolItemRemoved::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->payload === ['payload']
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_INIT;
        });
    }

    public function testGetPoolItems(): void
    {
        $pool = $this->service->add('test', ['payload']);

        $items = $this->service->getPoolItems('test');

        $this->assertCount(1, $items);
        $this->assertTrue($items->first()->is($pool));
    }

    public function testPoolItemCompleted()
    {
        Event::fake(PoolItemCompleted::class);
        $pool = $this->service->add('test', ['payload']);
        $this->service->complete($pool);

        $this->assertDatabaseHas('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_COMPLETED,
            'job' => 'test',
            'payload' => ['payload']
        ], 'mongodb');

        Event::assertDispatched(PoolItemCompleted::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->payload === ['payload']
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_COMPLETED;
        });
    }
}
