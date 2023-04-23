<?php

namespace App\Modules\Core\Tests\Unit\OAuth;

use App\Modules\Core\OAuth\Exceptions\TokenNotFoundException;
use App\Modules\Core\OAuth\OAuth1\Token\Token;
use App\Modules\Core\OAuth\Storage\Memory;
use App\Modules\Core\OAuth\Storage\Redis;
use App\Modules\Core\OAuth\Storage\Session;
use App\Modules\Core\OAuth\Storage\SymfonySession;
use App\Modules\Core\OAuth\Storage\TokenStorageInterface;
use Tests\TestCase;

class StorageTest extends TestCase
{
    /**
     * @dataProvider storageProvider
     */
    public function testRetrieveAccessNotFound(TokenStorageInterface $storage)
    {
        $this->expectException(TokenNotFoundException::class);
        $storage->retrieveAccessToken($this->faker->text);
    }

    /**
     * @dataProvider storageProvider
     */
    public function testRetrieveAccess(TokenStorageInterface $storage)
    {
        $storage->storeAccessToken('github', app(Token::class));
        $this->assertTrue($storage->hasAccessToken('github'));
        $this->assertInstanceOf(Token::class, $storage->retrieveAccessToken('github'));

        $this->expectException(TokenNotFoundException::class);
        $storage->clearAllTokens();
        $storage->retrieveAccessToken('github');
    }

    /**
     * @dataProvider storageProvider
     */
    public function testStoreAuthorizationState(TokenStorageInterface $storage)
    {
        $storage->storeAuthorizationState('github', 'state');
        $this->assertEquals('state', $storage->retrieveAuthorizationState('github'));
    }

    public function storageProvider()
    {
        return [
            [
                app(Memory::class),

            ],
            [
                app(Redis::class),

            ],
            [
                app(Session::class),

            ],
            [
                app(SymfonySession::class),
            ]
        ];
    }
}
