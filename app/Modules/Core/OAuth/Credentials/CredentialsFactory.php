<?php

namespace App\Modules\Core\OAuth\Credentials;

class CredentialsFactory
{
    public function make(string $provider): CredentialsInterface
    {
        return new Credentials(
            config('core.'. $provider .'.key'),
            config('core.'. $provider .'.secret'),
            config('core.'. $provider .'.callback'),
        );
    }
}
