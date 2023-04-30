<?php

namespace App\Modules\Flickr\Console\Queues;

class Photosets extends AbstractQueueCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:queues-photosets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    protected function getJob(): string
    {
        return \App\Modules\Flickr\Jobs\Queues\Photosets::class;
    }
}
