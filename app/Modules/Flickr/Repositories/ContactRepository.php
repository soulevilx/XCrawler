<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Core\Repositories\AbstractBaseRepository;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Events\CreatedContactsWithoutInformation;
use App\Modules\Flickr\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class ContactRepository extends AbstractBaseRepository
{
    public function __construct(protected Contact $model)
    {
    }

    public function insert(Collection $items): bool
    {
        $existsContacts = Contact::byNsids($items->pluck('nsid')->toArray())
            ->pluck('nsid')->toArray();

        $items = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsContacts)
        )->values();

        $now = Carbon::now();
        $items = $items->map(function ($item) use ($now) {
            return [
                'uuid' => Str::orderedUuid(),
                'nsid' => $item['nsid'],
                'username' => $item['username'] ?? null,
                'iconserver' => $item['iconserver'] ?? null,
                'iconfarm' => $item['iconfarm'] ?? null,
                'ignored' => $item['ignored'] ?? null,
                'rev_ignored' => $item['rev_ignored'] ?? null,
                'realname' => $item['realname'] ?? null,
                'friend' => $item['friend'] ?? null,
                'family' => $item['family'] ?? null,
                'path_alias' => $item['path_alias'] ?? null,
                'location' => $item['location'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        });

        if (!parent::insert($items)) {
            return false;
        }

        Event::dispatch(
            new CreatedBulkOfContacts($items->pluck('nsid')->toArray())
        );

        return true;
    }

    public function insertWithCheck(Collection $items, string $key)
    {
        $existsContacts = $this->getItems([
            'whereIn' => [
                'nsid' => $items->pluck($key)->toArray(),
            ],
        ])->pluck('nsid')->toArray();

        $notExistsContacts = $items->filter(
            fn($item) => !in_array($item[$key], $existsContacts)
        )->values();

        if ($notExistsContacts->isEmpty()) {
            return;
        }

        Event::dispatch(
            new CreatedContactsWithoutInformation ($notExistsContacts->pluck($key)->toArray())
        );

        $this->insert($notExistsContacts->map(
            fn($item) => [
                'nsid' => $item[$key],
            ]
        )->unique('nsid')->values());
    }
}
