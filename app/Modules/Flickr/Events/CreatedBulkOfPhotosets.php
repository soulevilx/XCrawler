<?php

namespace App\Modules\Flickr\Events;

class CreatedBulkOfPhotosets
{
    public function __construct(
        public array $photosets,
    ) {
    }
}
