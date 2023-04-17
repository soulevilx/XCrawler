<?php

namespace App\Modules\Core\Tests\Unit\Services;

use App\Modules\Core\Services\XClient\XClient;
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
                    'instance' => app('log'),
                ],
                'fakeResponseCode' => 200,
            ]
            , [
            'stream' => false,
        ]);

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
            ]
            , [
            'stream' => false,
        ]);

        $this->assertEquals(200, $client->request($this->faker->url)->getStatusCode());
        Cache::shouldReceive()->remember();
    }

    /**
     * @TODO Add more tests
     */
}
