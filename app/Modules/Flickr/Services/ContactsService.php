<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Jobs\Queues\Photosets;
use App\Modules\Flickr\Repositories\ContactsRepository;
use Illuminate\Support\Collection;

class ContactsService
{
    public function __construct(private ContactsRepository $repository)
    {
    }

    public function insertBulk(Collection $items)
    {
        $this->repository->insertBulk($items);
        $this->createPhotosPool($items);
        $this->createPhotosetsPool($items);
    }

    private function createPhotosPool(Collection $items)
    {
        /**
         * Create Photos pool
         * - Check pool if exists
         * - Filter exists items
         * - Add not exists item to pool
         */
        $existsPoolItems = $this->getPoolByJob(
            $items->pluck('nsid')->toArray(),
            Photos::class
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
        })->toArray();

        $this->insertPool($notExistsPoolItems);
    }

    private function createPhotosetsPool(Collection $items)
    {
        /**
         * Create Photosets pool
         * - Check pool if exists
         * - Filter exists items
         * - Add not exists item to pool
         */
        $existsPoolItems = $this->getPoolByJob(
            $items->pluck('nsid')->toArray(),
            Photosets::class
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
        })->toArray();

        $this->insertPool($notExistsPoolItems);
    }

    private function getPoolByJob(array $nsids, string $job): \Illuminate\Database\Eloquent\Collection
    {
        return Pool::whereIn('nsid', $nsids)
            ->where('job', $job)
            ->where('state_code', PoolService::STATE_CODE_INIT)
            ->get();
    }

    private function insertPool(array $items)
    {
        if (empty($items)) {
            return;
        }

        Pool::insert($items);
    }
}
