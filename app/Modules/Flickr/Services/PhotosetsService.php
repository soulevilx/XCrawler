<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotos;
use App\Modules\Flickr\Jobs\Queues\PhotosetPhotos;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Repositories\PhotosetsRepository;
use App\Modules\Flickr\Repositories\PhotosRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class PhotosetsService
{
    public function __construct(private PhotosetsRepository $repository)
    {
    }

    public function insertPhotosetsBulk(Collection $items)
    {
        $this->repository->insertBulk($items);
        $this->createPhotosPool($items);
    }

    private function createPhotosPool(Collection $items)
    {
        /**
         * Fetch photos for each photoset
         */

        $items = $items->groupBy('owner');

        foreach ($items as $owner => $photosets) {
            $existsPoolItems = Pool::where('nsid', $owner)
                ->where('job', PhotosetPhotos::class)
                ->where('state_code', PoolService::STATE_CODE_INIT)
                ->whereIn('id', $photosets->pluck('id')->toArray())
                ->pluck('id')->toArray();

            $notExistsPoolItems = $photosets->filter(
                fn($item) => !in_array($item['id'], $existsPoolItems)
            )->values()->map(function ($item, $key) {
                return [
                    'state_code' => PoolService::STATE_CODE_INIT,
                    'job' => PhotosetPhotos::class,
                    'id' => $item['id'],
                    'queue' => PoolService::QUEUE_API
                ];
            })->toArray();
        }

        if (empty($notExistsPoolItems)) {
            return;
        }

        Pool::insert($notExistsPoolItems);
    }

    public function createPhotosBulk(array $items)
    {
        /**
         * 1. Check and create photoset
         * 2. Create photos
         * 3. Link with photoset
         */

        $owner = $items['photoset']['owner'];
        $photoset = Photoset::firstOrCreate([
            'id' => $items['photoset']['id'],
            'owner' => $owner,
        ]);

        $photoRepository = app(PhotosRepository::class);
        $photos = collect($items['photoset']['photo']);
        $existsPhotos = $photoRepository->getPhotosByIds(
            $owner,
            $photos->pluck('id')->toArray()
        )->pluck('id')->toArray();

        $notExistsPhotos = $photos->filter(
            fn($item) => !in_array($item['id'], $existsPhotos)
        )->map(function ($item) use ($owner) {
            $item['owner'] = $owner;
            return $item;
        })->values()->toArray();


        if (empty($notExistsPhotos)) {
            return;
        }

        Photo::insert($notExistsPhotos);

        Event::dispatch(new CreatedBulkOfPhotos($notExistsPhotos));

        /**
         * Link photos with photoset
         */
        $photoIds = Photo::where('owner', $owner)->whereIn('id', $photos->pluck('id')->toArray())->pluck('_id')->toArray();
        $photoset->photos()->syncWithoutDetaching($photoIds);
    }
}
