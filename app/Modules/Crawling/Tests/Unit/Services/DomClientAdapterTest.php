<?php

namespace App\Modules\Crawling\Tests\Unit\Services;

use App\Modules\Core\Services\XClient\XClient;
use App\Modules\Crawling\Events\CrawlingFailed;
use App\Modules\Crawling\Events\CrawlingSuccess;
use App\Modules\Crawling\Models\RequestLog;
use App\Modules\Crawling\Services\CrawlingService;
use App\Modules\Crawling\Services\XClient\Adapters\DomClientAdapter;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class DomClientAdapterTest extends TestCase
{
    public function testRequestSuccess()
    {
        Event::fake([CrawlingSuccess::class]);
        $url = $this->faker->url;
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
        $response =  $service->request('GET', $url);
        $this->assertInstanceOf(
            Crawler::class,
            $response->getData()
        );
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals('OK', $response->getReasonPhrase());

        Event::assertDispatched(CrawlingSuccess::class);

        $this->assertDatabaseHas(
            RequestLog::TABLE_NAME,
            [
                'url' => $url,
                'status' => 200,
                'success' => true,
            ],
            'mongodb'
        );
    }

    public function testRequestClientException()
    {
        Event::fake([CrawlingFailed::class]);
        $url = $this->faker->url;
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) use ($url) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../Fixtures/onejav_new_10.html'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andThrowExceptions([
                        new ClientException(
                            'ClientException',
                            new ServerRequest('GET', $url),
                            new Response(404)
                        )
                    ]);
            })
        );

        $service = app()->makeWith(CrawlingService::class, [
            'adapter' => app(DomClientAdapter::class),
        ]);
        $service->request(
            'GET',
            $url,
            []
        );

        Event::assertDispatched(CrawlingFailed::class);

        $this->assertDatabaseHas(
            RequestLog::TABLE_NAME,
            [
                'url' => $url,
                'status' => 404,
                'success' => false,
            ],
            'mongodb'
        );
    }

    public function testRequestException()
    {
        Event::fake([CrawlingFailed::class]);
        $url = $this->faker->url;
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
                $mock->shouldReceive('request')
                    ->andThrowExceptions([
                        new \Exception(
                            'ClientException',
                        )
                    ]);
            })
        );

        $service = app()->makeWith(CrawlingService::class, [
            'adapter' => app(DomClientAdapter::class),
        ]);
        $service->request(
            'GET',
            $url
        );

        Event::assertDispatched(CrawlingFailed::class);

        $this->assertDatabaseHas(
            RequestLog::TABLE_NAME,
            [
                'url' => $url,
                'status' => null,
                'success' => false,
            ],
            'mongodb'
        );
    }
}
