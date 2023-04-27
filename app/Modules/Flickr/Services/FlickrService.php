<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Flickr\Services\Adapters\Contacts;
use App\Modules\Flickr\Services\Adapters\Favorites;
use App\Modules\Flickr\Services\Adapters\People;
use App\Modules\Flickr\Services\Adapters\PhotoSets;

class FlickrService
{
    public function contacts(): Contacts
    {
        return app(Contacts::class);
    }

    public function people(): People
    {
        return app(People::class);
    }

    public function favorites(): Favorites
    {
        return app(Favorites::class);
    }

    public function photosets(): PhotoSets
    {
        return app(PhotoSets::class);
    }
}
