<?php

namespace App\Modules\Flickr\Console\Queues;

use App\Modules\Core\Facades\Pool;
use App\Modules\Core\Repositories\PoolRepository;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

/**
 * Queues using here because of the following reasons:
 * - So many sub jobs are required to do but can't process at same time
 */
abstract class AbstractQueueCommand extends Command implements Isolatable
{
    public function __construct(
        protected PoolRepository $repository,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queues = Pool::getPoolItems($this->getJob(), config('flickr.pool.limit'));

        foreach ($queues as $queue) {
            $queue->job::dispatch($queue);
        }

        return 0;
    }

    abstract protected function getJob(): string;
}
