<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    public function testHandle()
    {
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../Fixtures/flickr_favorites.json'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andReturn($response);
            })
        );
        $this->artisan('flickr:favorites 123')
            ->assertExitCode(0);

        $this->assertDatabaseCount('flickr_photos', 98, 'mongodb');
    }
}
