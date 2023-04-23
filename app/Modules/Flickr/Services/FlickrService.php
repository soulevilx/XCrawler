<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Core\OAuth\OauthService;
use App\Modules\Core\Services\OAuth\Adapters\FlickrAdapter;
use App\Modules\Flickr\Services\Adapters\Contacts;

class FlickrService
{
    private mixed $client;

    public function __construct()
    {
        $this->client = app()->makeWith(OauthService::class, [
            'adapter' => app(FlickrAdapter::class),
        ]);
    }

   public function contacts()
   {
       return new Contacts($this->adapter);
   }
}
