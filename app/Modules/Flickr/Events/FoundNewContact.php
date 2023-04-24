<?php

namespace App\Modules\Flickr\Events;

class FoundNewContact
{
    public function __construct(
        public string $nsid,
    ) {
    }
}
