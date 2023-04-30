<?php

namespace App\Modules\Flickr\Repositories;

use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Models\Photoset;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;

class PhotosetsRepository
{
    public function getPhotosetsById(array $ids): Collection
    {
        return Photoset::whereIn('id', $ids)->get();
    }

    public function insertBulk(\Illuminate\Support\Collection $items)
    {
        $existsPhotosets = $this->getPhotosetsById($items->pluck('id')->toArray())->pluck('id')->toArray();
        $notExistsPhotosets = $items->filter(
            fn($item) => !in_array($item['id'], $existsPhotosets)
        )->values()->all();

        if (empty($notExistsPhotosets)) {
            return;
        }

        Photoset::insert($notExistsPhotosets);

        Event::dispatch(new CreatedBulkOfPhotosets($notExistsPhotosets));
    }
}
