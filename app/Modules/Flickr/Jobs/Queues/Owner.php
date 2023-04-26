<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Flickr\Models\Contact;

class Owner extends AbstractFlickrQueues
{
    public function process(): bool
    {
        /**
         * If contact already exists, skip
         */
        if (Contact::where('nsid', $this->item['nsid'])->exists()) {
            return true;
        }

        $owner = $this->service
            ->people()
            ->getInfo($this->item['nsid']);

        Contact::updateOrCreate([
            'nsid' => $owner['nsid'],
        ], $owner);

        return true;
    }
}
