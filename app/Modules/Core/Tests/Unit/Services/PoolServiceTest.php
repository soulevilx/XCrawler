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

        $this->service->add('test', ['nsid' => '123']);

        $this->assertDatabaseHas('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_INIT,
            'job' => 'test',
            'nsid' => "123"
        ], 'mongodb');

        Event::assertDispatched(PoolItemAdded::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->nsid === "123"
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_INIT;
        });
    }

    public function testAddWithoutDuplicate()
    {
        $this->service->add('test', ['test' => 'test'], PoolService::QUEUE_API);
        $this->service->add('test', ['test' => 'test'], PoolService::QUEUE_API);

        $this->assertDatabaseHas('pool', [
            'job' => 'test',
            'test' => 'test',
            'queue' => PoolService::QUEUE_API,
            'state_code' => PoolService::STATE_CODE_INIT,
        ], 'mongodb');

        $this->assertCount(1, $this->service->getItems(['where' => ['job' => 'test']]));
    }

    public function testPoolDeleted(): void
    {
        Event::fake(PoolItemRemoved::class);
        $item = $this->service->add('test', ['nsid' => '123']);
        $this->service->remove($item);

        $this->assertDatabaseMissing('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_INIT,
            'job' => 'test',
            'nsid' => "123"
        ], 'mongodb');

        Event::assertDispatched(PoolItemRemoved::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->nsid === "123"
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_INIT;
        });
    }

    public function testGetPoolItems(): void
    {
        $this->service->add('test', ['nsid' => '123']);
        $this->service->add('test2', ['nsid' => '123']);
        $items = $this->service->getItems(['where' => ['job' => 'test' ]]);

        $this->assertCount(1, $items);

        $this->assertDatabaseHas('pool', [
            'job' => 'test',
            'nsid' => "123",
            'state_code' => PoolService::STATE_CODE_PROCESSING,
        ], 'mongodb');
        $this->assertDatabaseHas('pool', [
            'job' => 'test2',
            'nsid' => "123",
            'state_code' => PoolService::STATE_CODE_INIT,
        ], 'mongodb');
    }

    public function testPoolItemCompleted()
    {
        Event::fake(PoolItemCompleted::class);
        $item = $this->service->add('test', ['nsid' => '123']);
        $this->service->complete($item);

        $this->assertDatabaseHas('pool', [
            'queue' => PoolService::QUEUE_LOW,
            'state_code' => PoolService::STATE_CODE_COMPLETED,
            'job' => 'test',
            'nsid' => "123"
        ], 'mongodb');

        Event::assertDispatched(PoolItemCompleted::class, function ($event) {
            return $event->pool->job === 'test'
                && $event->pool->nsid === "123"
                && $event->pool->queue === PoolService::QUEUE_LOW
                && $event->pool->state_code === PoolService::STATE_CODE_COMPLETED;
        });
    }
}
