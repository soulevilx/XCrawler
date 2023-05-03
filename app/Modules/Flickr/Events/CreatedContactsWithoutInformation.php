<?php

namespace App\Modules\Flickr\Events;

class CreatedContactsWithoutInformation
{
    public function __construct(
        public array $nsids,
    ) {
    }
}
