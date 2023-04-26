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

        /**
         * Model actually already deleted from database after pulled out from pool
         * - Only array provided here for processing
         * It would be issued when queue passed to job but failed. We have no way recover it
         */
        foreach ($queues as $queue) {
            $queue['job']::dispatch($queue);
        }

        return 0;
    }

    abstract protected function getJob(): string;
}
