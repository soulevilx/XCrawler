<?php

namespace App\Modules\Flickr\Jobs\Queues;

use App\Modules\Flickr\Models\Contact;

class Owner extends AbstractFlickrQueues
{
    public function process(): bool
    {
        if (!$contact = Contact::byNsid($this->item->nsid)->first()) {
            return true;
        }

        $owner = $this->service
            ->people()
            ->getInfo($this->item->nsid);

        $data = [
            'ispro' => $owner['ispro'],
            'is_deleted' => $owner['is_deleted'],
        ];
        unset($owner['ispro'], $owner['is_deleted']);
        $data['details'] = $owner;

        $contact->update($data);

        return true;
    }
}
