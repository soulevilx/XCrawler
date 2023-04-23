<?php

namespace App\Modules\Core\Tests\Unit\Services;

use App\Modules\Core\Events\CrawlingFailed;
use App\Modules\Core\Events\CrawlingSuccess;
use App\Modules\Core\Services\CrawlingService;
use App\Modules\Core\XClient\Adapters\DomClientAdapter;
use App\Modules\Core\XClient\Response\DomResponse;
use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CrawlingServiceTest extends TestCase
{
    public function testCrawlingSuccess()
    {
        Event::fake([CrawlingSuccess::class]);
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../Fixtures/onejav_new_10.html'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')->andReturn($response);
            })
        );

        $service = app()->makeWith(CrawlingService::class, [
            'adapter' => app(DomClientAdapter::class),
        ]);

        $url = $this->faker->url;
        $response = $service->request('GET', $url);
        $this->assertInstanceOf(DomResponse::class, $response);
        $this->assertTrue($response->isSuccess());

        Event::assertDispatched(CrawlingSuccess::class);

        $this->assertDatabaseHas('request_logs', [
            'url' => $url,
            'status' => 200,
            'success' => true,
        ], 'mongodb');
    }

    public function testCrawlingFailed()
    {
        Event::fake([CrawlingFailed::class]);
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')->andThrowExceptions([new \Exception('test')]);
            })
        );

        $service = app()->makeWith(CrawlingService::class, [
            'adapter' => app(DomClientAdapter::class),
        ]);

        $url = $this->faker->url;
        $response = $service->request('GET', $url);

        $this->assertInstanceOf(DomResponse::class, $response);
        $this->assertFalse($response->isSuccess());

        Event::assertNotDispatched(CrawlingSuccess::class);
        Event::assertDispatched(CrawlingFailed::class);

        $this->assertDatabaseHas('request_logs', [
            'url' => $url,
            'status' => null,
        ], 'mongodb');
    }
}
