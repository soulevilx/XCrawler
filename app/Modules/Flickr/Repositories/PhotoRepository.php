<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Core\Repositories\AbstractBaseRepository;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Models\Photo;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class PhotoRepository extends AbstractBaseRepository
{
    public function __construct(protected Photo $model)
    {
    }

    public function insert(Collection $items): bool
    {
        $photosByOwners = $items->groupBy('owner');
        /**
         * Create contact
         */
        $contactRepository = app(ContactRepository::class);
        $contactRepository->insertWithCheck($items, 'owner');

        foreach ($photosByOwners as $owner => $photos) {
            $existsPhotos = $this->getItems(
                [
                    'where' => compact('owner'),
                    'whereIn' => [
                        'id' => $photos->pluck('id')->toArray(),
                    ],
                ]
            )->pluck('id')->toArray();

            $photos = $photos->filter(
                fn($item) => !in_array($item['id'], $existsPhotos)
            )->values();

            $now = Carbon::now();
            $photos = $photos->map(function ($item) use ($now) {
                return [
                    'uuid' => Str::orderedUuid(),
                    'id' => $item['id'],
                    'owner' => $item['owner'],
                    'secret' => $item['secret'],
                    'server' => $item['server'],
                    'farm' => $item['farm'],
                    'title' => $item['title'] ?? null,
                    'ispublic' => $item['ispublic'] ?? 0,
                    'isfriend' => $item['isfriend'] ?? 0,
                    'isfamily' => $item['isfamily'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });

            if (!parent::insert($photos)) {
                continue;
            }

            Event::dispatch(new CreatedBulkOfPhotos($photos->pluck('id')->toArray()));
        }

        return true;
    }
}
