<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Favorites;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Repositories\ContactRepository;
use Illuminate\Support\Collection;

class ContactService
{

    public function __construct(private ContactRepository $repository, private PoolService $poolService)
    {
    }

    public function insert(Collection $items)
    {
        $this->repository->insert($items);
        $this->createPhotosPool($items);
        $this->createPhotosetsPool($items);
        $this->createFavoritesPool($items);
    }

    private function createPhotosPool(Collection $items)
    {
        /**
         * Create Photos pool
         * - Check pool if exists
         * - Filter exists items
         * - Add not exists item to pool
         */
        $existsPoolItems = $this->poolService->getItems(
            [
                'whereIn' => [
                    'nsid' => $items->pluck('nsid')->toArray(),
                ],
                'where' => [
                    'job' => Photos::class,
                ]
            ]
        )->pluck('nsid')->toArray();

        $notExistsPoolItems = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsPoolItems)
        )->values()->map(function ($item) {
            return [
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Photos::class,
                'nsid' => $item['nsid'],
                'queue' => PoolService::QUEUE_API
            ];
        });

        $this->poolService->insert($notExistsPoolItems);
    }

    private function createPhotosetsPool(Collection $items)
    {
        $existsPoolItems = $this->poolService->getItems(
            [
                'whereIn' => [
                    'nsid' => $items->pluck('nsid')->toArray(),
                ],
                'where' => [
                    'job' => Photosets::class,
                ]
            ]
        )->pluck('nsid')->toArray();

        $notExistsPoolItems = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsPoolItems)
        )->values()->map(function ($item) {
            return [
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Photosets::class,
                'nsid' => $item['nsid'],
                'queue' => PoolService::QUEUE_API
            ];
        });

        $this->poolService->insert($notExistsPoolItems);
    }

    private function createFavoritesPool(Collection $items)
    {
        $existsPoolItems = $this->poolService->getItems(
            [
                'whereIn' => [
                    'nsid' => $items->pluck('nsid')->toArray(),
                ],
                'where' => [
                    'job' => Favorites::class,
                ]
            ]
        )->pluck('nsid')->toArray();

        $notExistsPoolItems = $items->filter(
            fn($item) => !in_array($item['nsid'], $existsPoolItems)
        )->values()->map(function ($item) {
            return [
                'state_code' => PoolService::STATE_CODE_INIT,
                'job' => Favorites::class,
                'nsid' => $item['nsid'],
                'queue' => PoolService::QUEUE_API
            ];
        });

        $this->poolService->insert($notExistsPoolItems);
    }
}
