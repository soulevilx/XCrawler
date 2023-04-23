<?php

namespace App\Modules\Core\OAuth\OAuth1\Providers;

use App\Modules\Core\OAuth\ProviderInterface as BaseProviderInterface;
use App\Modules\Core\OAuth\Token\TokenInterface;
use App\Modules\Core\OAuth\Uri\UriInterface;

interface ProviderInterface extends BaseProviderInterface
{
    /** @const OAUTH_VERSION */
    public const OAUTH_VERSION = 1;

    /**
     * Retrieves and stores/returns the OAuth1 request token obtained from the service.
     *
     * @return TokenInterface $token
     */
    public function requestRequestToken(): TokenInterface;

    /**
     * Retrieves and stores/returns the OAuth1 access token after a successful authorization.
     *
     * @param string $token       the request token from the callback
     * @param string $verifier
     * @param string $tokenSecret
     *
     * @return TokenInterface $token
     */
    public function requestAccessToken($token, $verifier, $tokenSecret): TokenInterface;

    /**
     * @return UriInterface
     */
    public function getRequestTokenEndpoint(): UriInterface;
}
