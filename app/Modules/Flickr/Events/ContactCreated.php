<?php

namespace App\Modules\Flickr\Events;

use App\Modules\Flickr\Models\Contact;

class ContactCreated
{
    public function __construct(
        public Contact $contact,
    ) {
    }
}
