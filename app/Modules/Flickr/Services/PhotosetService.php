<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Core\Repositories\PoolRepository;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\PhotosetPhotos;
use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Repositories\PhotoRepository;
use App\Modules\Flickr\Repositories\PhotosetRepository;
use Illuminate\Support\Collection;

class PhotosetService
{
    public function __construct(private PhotosetRepository $repository, private PoolRepository $poolRepository)
    {
    }

    public function insert(Collection $items)
    {
        $this->repository->insert($items);
        $this->createPhotosetPool($items);
    }

    public function insertPhotos(array $items)
    {
        $owner = $items['photoset']['owner'];
        Contact::firstOrCreate(['nsid' => $owner]);
        $photoset = Photoset::firstOrCreate([
            'id' => $items['photoset']['id'],
            'owner' => $owner,
        ]);

        $photos = collect($items['photoset']['photo'])->map(function ($item) use ($owner) {
            $item['owner'] = $owner;
            return $item;
        });

        $photoRepository = app(PhotoRepository::class);
        $photoRepository->insert($photos);

        /**
         * Link photos with photoset
         */
        $photoIds = $photoRepository->getItems([
            'whereIn' => [
                'id' => $photos->pluck('id')->toArray(),
            ],
            'where' => compact('owner'),
        ])->pluck('id')->toArray();
        $photoset->photos()->syncWithoutDetaching($photoIds);
    }

    private function createPhotosetPool(Collection $items)
    {
        /**
         * Fetch photos for each photoset
         */

        $items = $items->groupBy('owner');

        foreach ($items as $owner => $photosets) {
            $existsPoolItems = $this->poolRepository->getItems([
                'where' => [
                    'nsid' => $owner,
                    'job' => PhotosetPhotos::class,
                ],
                'whereIn' => [
                    'id' => $photosets->pluck('id')->toArray(),
                ],
            ])->pluck('id')->toArray();

            $notExistsPoolItems = $photosets->filter(
                fn($item) => !in_array($item['id'], $existsPoolItems)
            )->values()->map(function ($item) {
                return [
                    'state_code' => PoolService::STATE_CODE_INIT,
                    'job' => PhotosetPhotos::class,
                    'id' => $item['id'],
                    'queue' => PoolService::QUEUE_API
                ];
            });

            $this->poolRepository->insert($notExistsPoolItems);
        }
    }
}
