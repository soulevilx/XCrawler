<?php

namespace App\Modules\Core\OAuth;

use App\Modules\Core\Models\Integration;
use App\Modules\Core\OAuth\OAuth1\Token\Token;

class ProviderFactory
{
    public function make(ProviderInterface $provider): ProviderInterface
    {
        $integration = Integration::where('service', $provider->service())->first();

        if ($integration) {
            $token = app(Token::class);
            $token->setAccessToken($integration->token);
            $token->setAccessTokenSecret($integration->token_secret);
            $provider->getStorage()->storeAccessToken(
                $provider->service(),
                $token
            );
        }

        return $provider;
    }
}
