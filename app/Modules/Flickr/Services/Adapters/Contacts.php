<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\ContactCreated;
use App\Modules\Flickr\Events\CreatedBulkOfContacts;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class Contacts extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 1000;
    public const LIST_ENTITY = 'contact';
    public const LIST_ENTITIES = 'contacts';

    protected string $getListMethod = 'flickr.contacts.getList';

    public const ERROR_CODE_INVALID_SORT_PARAMETER = 1;

    public function create(array $item): Contact
    {
        $contact = Contact::create($item);
        Event::dispatch(new ContactCreated($contact));

        return $contact;
    }

    public function createMany(Collection $items): void
    {
        $existsContacts = Contact::whereIn('nsid', $items->pluck('nsid'))->pluck('nsid')->toArray();
        $notExistsContacts = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsContacts)
        )->values()->toArray();

        if (!empty($notExistsContacts)) {
            Contact::insert($notExistsContacts);

            Event::dispatch(new CreatedBulkOfContacts($notExistsContacts));
        }

        /**
         * Create Photos pool
         * - Check pool if exists
         * - Filter exists items
         * - Add not exists item to pool
         */
        $existsPoolItems = Pool::whereIn('nsid', $items->pluck('nsid')->toArray())
            ->where('job', Photos::class)
            ->where('state_code', PoolService::STATE_CODE_INIT)
            ->pluck('nsid')->toArray();

        $notExistsPoolItems = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsPoolItems)
        )->values()->map(function ($item, $key) {
            return [
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Photos::class,
                'nsid' => $item['nsid'],
                'queue' => PoolService::QUEUE_API
            ];
        })->toArray();

        if (!empty($notExistsPoolItems)) {
            Pool::insert($notExistsPoolItems);
        }
    }
}
