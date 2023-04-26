<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Pool;
use Illuminate\Database\Eloquent\Collection;

class PoolRepository
{
    public function __construct(
        protected Pool $queue,
    ) {
    }

    public function getItems(
        string $job,
        string $stateCode,
        int $limit
    ): Collection {
        return $this->queue
            ->byJob($job)
            ->where('state_code', $stateCode)
            ->limit($limit)
            ->get();
    }
}
