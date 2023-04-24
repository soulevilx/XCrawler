<?php

namespace App\Modules\Core\Tests\Unit;

use App\Modules\Core\Services\Pool\PoolService;
use Tests\TestCase;

class PoolServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(PoolService::class);
    }

    public function testAdd()
    {
        $this->service->add('test', ['test' => 'test'], PoolService::QUEUE_API);
        $this->service->add('test', ['test' => 'test'], PoolService::QUEUE_API);

        $this->assertDatabaseHas('pool', [
            'job' => 'test',
            'payload' => ['test' => 'test'],
            'queue' => PoolService::QUEUE_API,
            'state_code' => PoolService::STATE_CODE_INIT,
        ], 'mongodb');

        $this->assertCount(1, $this->service->getPoolItems('test'));
    }
}
