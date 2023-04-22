<?php

namespace App\Modules\Core\OAuth\OAuth1\Signature;

use App\Modules\Core\OAuth\Uri\UriInterface;

interface SignatureInterface
{

    public function setHashingAlgorithm(string $algorithm): self;


    public function setTokenSecret(string $token): self;

    /**
     * @param  UriInterface  $uri
     * @param  array  $params
     * @param  string  $method
     * @return string
     */
    public function getSignature(UriInterface $uri, array $params, string $method = 'POST'): string;
}
