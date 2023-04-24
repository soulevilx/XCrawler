<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;

class PoolRepository
{
    public function __construct(
        protected Pool $queue,
    ) {
    }

    public function getItems(string $job, int $limit)
    {
        return $this->queue
            ->where('job', $job)
            ->where('state_code', PoolService::STATE_CODE_INIT)
            ->limit($limit)
            ->get();
    }
}
