<?php

namespace App\Modules\Core\Services\Pool;

use App\Modules\Core\Events\PoolItemAdded;
use App\Modules\Core\Events\PoolItemCompleted;
use App\Modules\Core\Events\PoolItemRemoved;
use App\Modules\Core\Models\Pool;
use App\Modules\Core\Repositories\PoolRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;

/**
 * Because performance issue we won't store payload as array. Instead of it
 * - Each payload item will be present as each column
 */
class PoolService
{
    public const QUEUE_LOW = 'low';
    public const QUEUE_MEDIUM = 'medium';
    public const QUEUE_HIGH = 'high';

    public const QUEUE_API = 'api';

    public const STATE_CODE_INIT = 'INIT';
    public const STATE_CODE_PROCESSING = 'PROCESSING';
    public const STATE_CODE_COMPLETED = 'COMPLETED';

    public function __construct(private PoolRepository $repository)
    {
    }

    public function insert(\Illuminate\Support\Collection $items): void
    {
        $this->repository->insert($items);
    }

    public function add(string $job, array $payload = [], ?string $queue = null): Pool
    {
        $cache = Cache::store('redis');
        $conditions = md5(serialize(['state_code' => self::STATE_CODE_INIT, 'job' => $job, ...$payload]));

        return $cache->remember(
            $conditions,
            config('core.pool.ttl', 60),
            function () use ($job, $payload, $queue) {
                $pool = Pool::firstOrCreate(
                    ['state_code' => self::STATE_CODE_INIT, 'job' => $job, ...$payload],
                    ['queue' => $queue ?? self::QUEUE_LOW]
                );
                Event::dispatch(new PoolItemAdded($pool));

                return $pool;
            }
        );
    }

    public function remove(Pool $pool): void
    {
        $pool->delete();

        Event::dispatch(new PoolItemRemoved($pool));
    }

    public function getItems(array|\Illuminate\Support\Collection $filter = []): Collection
    {
        $items = $this->repository->getItems($filter);

        Pool::whereIn('_id', $items->pluck('_id')->toArray())
            ->update(['state_code' => self::STATE_CODE_PROCESSING]);

        return $items;
    }

    public function processing(Pool $pool): void
    {
        $pool->update(['state_code' => self::STATE_CODE_PROCESSING]);
    }

    public function complete(Pool $pool): void
    {
        $pool->update(['state_code' => self::STATE_CODE_COMPLETED]);

        Event::dispatch(new PoolItemCompleted($pool));
    }
}
