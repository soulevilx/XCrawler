<?php

namespace App\Modules\Core\Tests\Unit\XClient;

use App\Modules\Core\XClient\XClient;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class XClientTest extends TestCase
{
    public function testEnableLogging()
    {
        $client = app(XClient::class);
        $client->init(
            [
                'logger' => [
                    'instance' => app(Logger::class),
                ],
                'fakeResponseCode' => 200,
            ],
            [
                'stream' => false,
            ]
        );


        $this->assertEquals(200, $client->request($this->faker->url)->getStatusCode());
        Log::shouldReceive()->info();
    }

    public function testEnableCache()
    {
        $client = app(XClient::class);
        $client->init(
            [
                'cache' => [
                    'instance' => app('cache'),
                    'ttl' => 60,
                ],
                'fakeResponseCode' => 200,
            ],
            [
                'stream' => false,
            ]
        );

        $this->assertEquals(200, $client->request($this->faker->url)->getStatusCode());
        Cache::shouldReceive()->remember();
    }
}
