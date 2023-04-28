<?php

namespace App\Modules\Flickr\Tests;

use App\Modules\Core\Models\Download;
use App\Modules\Core\Models\Integration;
use App\Modules\Core\OAuth\OAuth1\Providers\Flickr;
use App\Modules\Core\XClient\XClient;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\App;
use Mockery;
use Mockery\MockInterface;
use OAuth\Common\Http\Uri\Uri;

class TestCase extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (App::environment('testing')) {
            Contact::truncate();
            Photo::truncate();
            Photoset::truncate();
            Download::truncate();
            Integration::truncate();

            Integration::create([
                'service' => 'flickr',
                'token' => 'test',
                'token_secret' => 'test',
            ]);
        }

        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                for ($index = 1; $index <= 2; $index++) {
                    $mock->shouldReceive('request')
                        ->withSomeOfArgs(
                            $this->getUri('flickr.contacts.getList')->getAbsoluteUri(),
                            $index === 1 ? ['per_page' => 1000] : ['per_page' => 1000, 'page' => $index]
                        )
                        ->andReturn(new Response(
                            200,
                            [],
                            file_get_contents(__DIR__.'/Fixtures/flickr_contacts_'. $index.'.json'),
                            '1.1',
                            'OK'
                        ));
                }

                for ($index = 1; $index <= 4; $index++) {
                    $mock->shouldReceive('request')
                        ->withSomeOfArgs(
                            $this->getUri('flickr.favorites.getList')->getAbsoluteUri(),
                            $index === 1 ? ['per_page' => 500, 'user_id' => '123'] : ['per_page' => 500, 'user_id' => '123', 'page' => $index]
                        )
                        ->andReturn(new Response(
                            200,
                            [],
                            file_get_contents(__DIR__.'/Fixtures/flickr_favorites_'. $index.'.json'),
                            '1.1',
                            'OK'
                        ));
                }

                $mock->shouldReceive('request')
                    ->withSomeOfArgs(
                        $this->getUri('flickr.people.getPhotos')->getAbsoluteUri(),
                        ['per_page' => 500, 'user_id' => '123'],
                    )
                    ->andReturn(new Response(
                        200,
                        [],
                        file_get_contents(__DIR__.'/Fixtures/flickr_photos_1.json'),
                        '1.1',
                        'OK'
                    ));

                $mock->shouldReceive('request')
                    ->withSomeOfArgs(
                        $this->getUri('flickr.photosets.getList')->getAbsoluteUri(),
                        ['per_page' => 500, 'user_id' => '123'],
                    )
                    ->andReturn(new Response(
                        200,
                        [],
                        file_get_contents(__DIR__.'/Fixtures/flickr_photosets.json'),
                        '1.1',
                        'OK'
                    ));
            })
        );
    }

    private function getUri(string $method): Uri
    {
        $uri = new Uri(Flickr::OAUTH_REST_ENDPOINT);

        $uri->addToQuery('method', $method);
        $uri->addToQuery('format', 'json');
        $uri->addToQuery('nojsoncallback', '1');

        return $uri;
    }
}
