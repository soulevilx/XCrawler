<?php

namespace App\Modules\Flickr\Tests\Feature\Console;

use App\Modules\Flickr\Tests\TestCase;

class DownloadTest extends TestCase
{
    public function testDownloadPhotoset()
    {
        $this->artisan('flickr:downloads-photoset', ['nsid' => '94529704@N02'])
            ->assertExitCode(0);

        $this->artisan('flickr:photosets 123');
    }
}
