<?php

namespace App\Modules\Core\Tests\Unit\XClient;

use App\Modules\Core\XClient\Factory;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    public function testFakeResponseCode(): void
    {
        $factory = app()->makeWith(Factory::class, ['fakeResponseCode' => 200]);
        $client = $factory->enableRetries()->make();

        $this->assertEquals(200, $client->get($this->faker->url)->getStatusCode());
    }
}
