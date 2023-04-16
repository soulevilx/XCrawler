<?php

namespace App\Modules\Crawling\Tests\Unit\Services;

use App\Modules\Core\Services\XClient\XClient;
use App\Modules\Crawling\Events\CrawlingFailed;
use App\Modules\Crawling\Events\CrawlingSuccess;
use App\Modules\Crawling\Services\CrawlingService;
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
        $service = app(CrawlingService::class);;
        $this->assertInstanceOf(
            Crawler::class,
            $service->request(
                'GET',
                $this->faker->url,
                []
            )->getData());

        Event::assertDispatched(CrawlingSuccess::class);
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

        $service = app(CrawlingService::class);
        $service->request(
            'GET',
            $this->faker->url,
            []
        );

        Event::assertDispatched(CrawlingFailed::class);
    }

    public function testRequestException()
    {
        Event::fake([CrawlingFailed::class]);
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

        $service = app(CrawlingService::class);
        $service->request(
            'GET',
            $this->faker->url,
            []
        );

        Event::assertDispatched(CrawlingFailed::class);
    }
}
