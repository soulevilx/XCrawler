<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Jobs\Queues\Owner;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

class PhotosRepository
{
    public function getPhotosByIds(string $owner, array $photoIds): Collection
    {
        return Photo::where('owner', $owner)->whereIn('id', $photoIds)->get();
    }

    public function insertBulk(\Illuminate\Support\Collection $items)
    {
        $photosByOwners = $items->groupBy('owner');
        $owners = $photosByOwners->keys();

        foreach ($photosByOwners as $owner => $photos) {
            $existsPhotos = $this->getPhotosByIds($owner, $photos->pluck('id')->toArray())->pluck('id')->toArray();
            $notExistsPhotos = $photos->filter(
                fn($item) => !in_array($item['id'], $existsPhotos)
            )->values()->toArray();

            if (!empty($notExistsPhotos)) {
                Photo::insert($notExistsPhotos);

                Event::dispatch(new CreatedBulkOfPhotos($notExistsPhotos));
            }
        }

        /**
         * Register owners in pool
         */

        $existsContacts = app(ContactsRepository::class)->getContactsByNsids($owners->toArray())->pluck('nsid')->toArray();
        $notExistsContacts = $owners->filter(
            fn($item) => !in_array($item, $existsContacts)
        )->toArray();

        if (empty($notExistsContacts)) {
            return;
        }

        $registeredPoolItems = Pool::where('job', Owner::class)
            ->whereIn('nsid', $notExistsContacts)->pluck('nsid')->toArray();

        $notExistsContactsInPool = collect(array_diff($notExistsContacts, $registeredPoolItems));
        $notExistsContactsInPool = $notExistsContactsInPool->map(function ($item) {
            return [
                'nsid' => $item,
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Owner::class,
                'queue' => PoolService::QUEUE_API
            ];
        })->values()->toArray();

        if (empty($notExistsContactsInPool)) {
            return;
        }

        Pool::insert($notExistsContactsInPool);
    }
}
