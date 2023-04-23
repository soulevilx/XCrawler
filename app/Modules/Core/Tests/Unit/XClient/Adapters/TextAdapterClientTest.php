<?php

namespace App\Modules\Core\Tests\Unit\XClient\Adapters;

use App\Modules\Core\XClient\Adapters\TextClientAdapter;
use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TextAdapterClientTest extends TestCase
{
    public function testTextAdapter(): void
    {
        $url = $this->faker->url;
        $text = $this->faker->text;
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) use ($text) {
                $response = new Response(
                    200,
                    [],
                    $text,
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')->andReturn($response);
            })
        );
        $client = app(TextClientAdapter::class);
        $response = $client->request('GET', $url);
        $this->assertEquals(
            200,
            $response->getStatusCode()
        );

        $this->assertEquals($text, $response->getRaw());
        $this->assertEquals($text, $response->getData());
    }
}
