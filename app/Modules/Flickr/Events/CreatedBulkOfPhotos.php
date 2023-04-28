<?php

namespace App\Modules\Flickr\Events;

class CreatedBulkOfPhotos
{
    public function __construct(
        public array $photos,
    ) {
    }
}
