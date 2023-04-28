<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Auth\Oauth;

class Auth extends BaseAdapter
{
    public function oauth(): Auth\Oauth
    {
        return app(Oauth::class);
    }
}
