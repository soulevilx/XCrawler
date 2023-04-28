<?php

namespace App\Modules\Flickr\Events;

class CreatedBulkOfContacts
{
    public function __construct(
        public array $nsid,
    ) {
    }
}
