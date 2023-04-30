<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

class ContactsRepository
{
    public function getContactsByNsids(array $nsids): Collection
    {
        return Contact::whereIn('nsid', $nsids)->get();
    }

    public function insertBulk(\Illuminate\Support\Collection $items)
    {
        $existsContacts = $this->getContactsByNsids($items->pluck('nsid')->toArray())
            ->pluck('nsid')->toArray();

        $items = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsContacts)
        )->values()->toArray();

        if (empty($items)) {
            return;
        }

        Contact::insert($items);

        Event::dispatch(new CreatedBulkOfContacts($items));
    }
}
