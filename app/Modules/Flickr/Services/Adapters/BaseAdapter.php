<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\OAuth\OAuth1\Providers\Flickr;
use App\Modules\Core\OAuth\ProviderFactory;
use App\Modules\Flickr\Services\Adapters\Interfaces\AdapterInterface;

class BaseAdapter implements AdapterInterface
{
    protected Flickr $provider;

    public function __construct()
    {
        $this->provider = app(ProviderFactory::class)->make(app(Flickr::class));
    }
}
