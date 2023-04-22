<?php

namespace App\Modules\Core\OAuth\Credentials;

interface CredentialsInterface
{

    public function getCallbackUrl(): string;

    public function getConsumerId(): string;


    public function getConsumerSecret(): string;
}
