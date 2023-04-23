<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\Services\OAuth\Adapters\FlickrAdapter;

class Contacts
{
    public const PER_PAGE = 1000;

    public const ERROR_CODE_INVALID_SORT_PARAMETER = 1;

    public function __construct(private FlickrAdapter $adapter)
    {
    }
}
