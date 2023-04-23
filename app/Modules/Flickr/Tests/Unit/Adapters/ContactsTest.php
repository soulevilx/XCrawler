<?php

namespace App\Modules\Flickr\Tests\Unit\Adapters;

use App\Modules\Flickr\Services\FlickrService;
use Tests\TestCase;

class ContactsTest extends TestCase
{
    public function testGetList()
    {
        $service = app(FlickrService::class);
        $contacts = $service->contacts()->getList();
    }
}
