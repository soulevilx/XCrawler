<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Queue;

class QueueRepository
{
    public function __construct(
        protected Queue $queue,
    ) {
    }

    public function getQueues(string $job, ?int $limit = null)
    {
        return $this->queue
            ->where('job', $job)
            ->where('state_code', Queue::STATE_CODE_INIT)
            ->limit($limit ?? config('core.queues.limit', 10))
            ->get();
    }
}
