<?php

namespace App\Modules\Flickr\Console\Queues;

use App\Modules\Core\Repositories\QueueRepository;
use Illuminate\Console\Command;

abstract class AbstractQueueCommand extends Command
{
    public function __construct(
        protected QueueRepository $repository,
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
        $repository = app(QueueRepository::class);
        $queues = $repository->getQueues($this->getJob());

        foreach ($queues as $queue) {
            $queue->job::dispatch($queue->payload)->onQueue($queue);
        }
    }

    abstract protected function getJob(): string;
}
