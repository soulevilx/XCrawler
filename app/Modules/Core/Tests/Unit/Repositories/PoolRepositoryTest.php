<?php

namespace App\Modules\Core\Tests\Unit\Repositories;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Repositories\PoolRepository;
use Tests\TestCase;

class PoolRepositoryTest extends TestCase
{
    public function testGetItems()
    {
        Pool::create([
            'job' => 'test',
            'nsid' => 'test',
            'state_code' => 'test',
        ]);
        $pool = Pool::create([
            'job' => 'test',
            'nsid' => $this->faker->uuid,
            'state_code' => 'test',
        ]);

        $repository = app(PoolRepository::class);

        $items = $repository->getItems([
            'job' => 'test',
            'nsid' => 'test',
            'state_code' => 'test',
        ]);

        $this->assertCount(2, $items);

        $items = $repository->getItems([
            'job' => 'test',
            'state_code' => 'test',
            'where' => [
                'nsid' => $pool->nsid,
            ]
        ]);

        $this->assertEquals($items->first()->nsid, $pool->nsid);
    }
}
