<?php

namespace App\Modules\Flickr\Console\Queues;

use App\Modules\Core\Facades\Pool;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

/**
 * Queues using here because of the following reasons:
 * - So many sub jobs are required to do but can't process at same time
 */
abstract class AbstractQueueCommand extends Command implements Isolatable
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $queues = Pool::getItems([
            'where' => ['job' => $this->getJob()],
            'limit' => config('flickr.pool.limit'),
        ]);

        $this->output->progressStart($queues->count());
        foreach ($queues as $queue) {
            $queue->job::dispatch($queue)->onQueue($queue->queue);
            $this->output->progressAdvance();
        }

        return 0;
    }

    abstract protected function getJob(): string;
}
