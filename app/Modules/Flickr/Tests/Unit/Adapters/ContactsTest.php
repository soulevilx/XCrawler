<?php

namespace App\Modules\Flickr\Tests\Unit\Adapters;

use App\Modules\Core\Models\Integration;
use App\Modules\Core\XClient\XClient;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\FlickrService;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    public function testGetList()
    {
        Event::fake(FetchedFlickrItems::class);
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../Fixtures/flickr_contacts_1.json'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andReturn($response);
            })
        );

        Integration::create([
            'service' => 'flickr',
            'token' => 'test',
            'token_secret' => 'test',
        ]);

        app(FlickrService::class)->contacts()->getList();

        Event::assertDispatched(FetchedFlickrItems::class, static function ($event) {
            return $event->data['contacts']['page'] === 1
                && $event->data['contacts']['pages'] === 2
                && $event->data['contacts']['per_page'] == 1000
                && $event->data['contacts']['total'] == 1109;
        });
    }
}
