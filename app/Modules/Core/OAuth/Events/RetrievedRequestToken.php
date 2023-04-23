<?php

namespace App\Modules\Core\OAuth\Events;

use App\Modules\Core\OAuth\Token\TokenInterface;

class RetrievedRequestToken
{
    public function __construct(public TokenInterface $token)
    {
    }
}
