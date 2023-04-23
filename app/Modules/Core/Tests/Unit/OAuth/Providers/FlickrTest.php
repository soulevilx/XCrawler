<?php

namespace App\Modules\Core\Tests\Unit\OAuth\Providers;

use App\Modules\Core\OAuth\Events\RetrievedRequestToken;
use App\Modules\Core\OAuth\OAuth1\Providers\Flickr;
use App\Modules\Core\OAuth\ProviderFactory;
use App\Modules\Core\XClient\XClient;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class FlickrTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    'oauth_callback_confirmed=true&oauth_token=72157720879972459-8a284da9adb57124&oauth_token_secret=f00f64f84c872f1f',
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('post')
                    ->with(Flickr::OAUTH_REQUEST_TOKEN_ENDPOINT)
                    ->andReturn($response);
            })
        );
    }

    public function testRequestRequestToken()
    {
        Event::fake([RetrievedRequestToken::class]);

        $provider = app(ProviderFactory::class)->make(app(Flickr::class));
        $provider->requestRequestToken();

        Event::assertDispatched(RetrievedRequestToken::class, function ($event) {
            return
                $event->token->getRequestToken() === '72157720879972459-8a284da9adb57124'
                && $event->token->getRequestTokenSecret() === 'f00f64f84c872f1f';
        });
    }

    public function testGetAuthorizationUri()
    {
        $provider = app(ProviderFactory::class)->make(app(Flickr::class));
        $this->assertEquals(
            'https://www.flickr.com/services/oauth/authorize?oauth_token=72157720879972459-8a284da9adb57124&perms=read',
            $provider->getAuthorizationUri([
                'oauth_token' => $provider->requestRequestToken()->getRequestToken(),
                'perms' => 'read'
            ])->getAbsoluteUri()
        );
    }

    public function testRetrieveAccessToken()
    {
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    'fullname=Viet%20Vu&oauth_token=72157720847599996-599ea1b1486d58e9&oauth_token_secret=b956e09ef9999a7e&user_nsid=94529704%40N02&username=SoulEvilX',
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andReturn($response);
            })
        );

        $provider = app(ProviderFactory::class)->make(app(Flickr::class));
        $token = $provider->retrieveAccessToken('1234567890');

        $this->assertEquals('72157720847599996-599ea1b1486d58e9', $token->getAccessToken());
        $this->assertEquals('b956e09ef9999a7e', $token->getAccessTokenSecret());
        $this->assertEquals(
            $token,
            $provider->getStorage()->retrieveAccessToken($provider->service())
        );
    }

    public function testRequest()
    {
        $json = '{ "user": { "id": "94529704@N02", "username": { "_content": "SoulEvilX" }, "path_alias": "soulevilx" }, "stat": "ok" }';
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) use ($json) {
                $response = new Response(
                    200,
                    [],
                    $json,
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')
                    ->andReturn($response);
            })
        );

        $provider = app(ProviderFactory::class)->make(app(Flickr::class));
        $response = $provider->request('flickr.test.login');
        $this->assertTrue($response->isSuccess());
        $this->assertEquals($json, $response->getRaw());
    }
}
