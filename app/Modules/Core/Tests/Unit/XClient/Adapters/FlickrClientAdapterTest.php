<?php

namespace App\Modules\Core\Tests\Unit\XClient\Adapters;

use App\Modules\Core\XClient\Adapters\FlickrClientAdapter;
use App\Modules\Core\XClient\Response\BaseResponse;
use App\Modules\Core\XClient\Response\FlickrResponse;
use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class FlickrClientAdapterTest extends TestCase
{
    public function testGettingRequestToken()
    {
        $textBody = $this->faker->text;
        $jsonBody = [
            'stat' => 'ok',
        ];

        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) use ($textBody, $jsonBody) {
                $response = new Response(
                    200,
                    [],
                    $textBody,
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->withArgs(['oauth', [], 'GET'])
                    ->andReturn($response);

                $response = new Response(
                    200,
                    [],
                    json_encode($jsonBody),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('request')
                    ->withArgs(['json', [], 'GET'])
                    ->andReturn($response);
            })
        );

        $service = app(FlickrClientAdapter::class);

        $response = $service->request('GET', 'oauth');
        $this->assertInstanceOf(BaseResponse::class, $response);
        $this->assertEquals($textBody, $response->getData());

        $response = $service->request('GET', 'json');
        $this->assertInstanceOf(FlickrResponse::class, $response);
        $this->assertEquals($jsonBody, $response->getData());
    }
}
