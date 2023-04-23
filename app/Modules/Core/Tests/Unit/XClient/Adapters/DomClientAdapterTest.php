<?php

namespace App\Modules\Core\Tests\Unit\XClient\Adapters;

use App\Modules\Core\XClient\Adapters\DomClientAdapter;
use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class DomClientAdapterTest extends TestCase
{
    public function testRequestSuccess()
    {
        $url = $this->faker->url;
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../../Fixtures/onejav_new_10.html'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')->andReturn($response);
            })
        );
        $service = app(DomClientAdapter::class);
        $response = $service->request('GET', $url);
        $this->assertTrue($response->isSuccess());
        $this->assertInstanceOf(
            Crawler::class,
            $response->getData()
        );
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testRequestClientException()
    {
        $url = $this->faker->url;
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) use ($url) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andThrowExceptions([
                        new ClientException(
                            'ClientException',
                            new ServerRequest('GET', $url),
                            new Response(404)
                        ),
                    ]);
            })
        );

        $response = app(DomClientAdapter::class)->request('GET', $url, []);
        $this->assertFalse($response->isSuccess());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testRequestException()
    {
        $url = $this->faker->url;
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andThrowExceptions([
                        new \Exception(
                            'ClientException',
                        ),
                    ]);
            })
        );

        $response = app(DomClientAdapter::class)->request('GET', $url, []);
        $this->assertFalse($response->isSuccess());
    }
}
