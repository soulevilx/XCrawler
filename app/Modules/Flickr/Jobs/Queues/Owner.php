<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Flickr\Models\Contact;

class Owner extends AbstractFlickrQueues
{
    public function process(): bool
    {
        $owner = $this->service
            ->people()
            ->getInfo($this->model->payload['nsid']);

        Contact::updateOrCreate([
            'nsid' => $owner['nsid'],
        ], $owner);

        return true;
    }
}
