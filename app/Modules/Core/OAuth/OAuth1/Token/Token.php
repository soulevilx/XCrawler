<?php

namespace App\Modules\Core\OAuth\OAuth1\Token;

use App\Modules\Core\OAuth\Token\AbstractBaseToken;

class Token extends AbstractBaseToken implements TokenInterface
{
    protected string $requestToken;

    protected string $requestTokenSecret;

    protected string $accessTokenSecret;

    /**
     * @param  string  $requestToken
     */
    public function setRequestToken(string $requestToken): void
    {
        $this->requestToken = $requestToken;
    }

    public function getRequestToken(): string
    {
        return $this->requestToken;
    }

    public function setRequestTokenSecret(string $requestTokenSecret): void
    {
        $this->requestTokenSecret = $requestTokenSecret;
    }

    public function getRequestTokenSecret(): string
    {
        return $this->requestTokenSecret;
    }

    public function setAccessTokenSecret(string $accessTokenSecret): void
    {
        $this->accessTokenSecret = $accessTokenSecret;
    }

    public function getAccessTokenSecret(): string
    {
        return $this->accessTokenSecret;
    }
}
