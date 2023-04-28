<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class People extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 500;

    protected string $getListMethod = 'flickr.people.getPhotos';
    public const LIST_ENTITY = 'photo';
    public const LIST_ENTITIES = 'photos';

    public function createMany(Collection $items): void
    {
        $photosByOwners = $items->groupBy('owner');
        $owners = $photosByOwners->keys();

        foreach ($photosByOwners as $owner => $photos) {
            $existsPhotos = Photo::where('owner', $owner)
                ->whereIn('id', $photos->pluck('id')->toArray())
                ->pluck('id')->toArray();
            $notExistsPhotos = $photos->filter(
                fn($item) => !in_array($item['id'], $existsPhotos)
            )->values()->toArray();

            if (!empty($notExistsPhotos)) {
                Photo::insert($notExistsPhotos);

                Event::dispatch(new CreatedBulkOfPhotos($notExistsPhotos));
            }
        }

        /**
         * Create contacts
         */

        $existsContacts = Contact::whereIn('nsid', $owners->toArray())
            ->pluck('nsid')->toArray();

        /**
         * Filter exists contacts
         */
        $notExistsContacts = $owners->filter(
            fn($item) => !in_array($item, $existsContacts)
        )->values()->map(function ($item, $key) {
            return [
                'nsid' => $item,
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Owner::class,
                'queue' => PoolService::QUEUE_API
            ];
        })->toArray();

        if (!empty($notExistsContacts)) {
            Pool::insert($notExistsContacts);
        }
    }

    public function getInfo(string $nsid): array
    {
        return $this->provider->request(
            'flickr.people.getInfo',
            ['user_id' => $nsid]
        )->getData()['person'];
    }
}
