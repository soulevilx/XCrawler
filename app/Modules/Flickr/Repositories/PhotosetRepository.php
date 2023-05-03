<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Core\Repositories\AbstractBaseRepository;
use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Models\Photoset;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class PhotosetRepository extends AbstractBaseRepository
{
    public function __construct(protected Photoset $model)
    {
    }

    public function insert(Collection $items): bool
    {
        $existsPhotosets = $this->getItems(
            [
                'whereIn' => [
                    'id' => $items->pluck('id')->toArray(),
                ],
            ]
        )->pluck('id')->toArray();
        $items = $items->filter(
            fn($item) => !in_array($item['id'], $existsPhotosets)
        )->values();

        $now = Carbon::now();
        $items = $items->map(function ($item) use ($now) {
            return [
                'uuid' => Str::orderedUuid(),
                'id' => $item['id'],
                'owner' => $item['owner'],
                'secret' => $item['secret'],
                'server' => $item['server'],
                'farm' => $item['farm'],
                'title' => isset($item['title'])
                    ? is_array($item['title']) ? json_encode($item['title']) : $item['title']
                    : null,
                'description' => isset($item['description'])
                    ? is_array($item['description']) ? json_encode($item['description']) : $item['description']
                    : null,
                'count_photos' => $item['count_photos'] ?? 0,
                'count_videos' => $item['count_videos'] ?? 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        });
        /**
         * Verify if owner already exists
         */

        $contactRepository = app(ContactRepository::class);
        $contactRepository->insertWithCheck($items, 'owner');

        if (!parent::insert($items)) {
            return false;
        }

        Event::dispatch(new CreatedBulkOfPhotosets($items->pluck('id')->toArray()));

        return true;
    }
}
