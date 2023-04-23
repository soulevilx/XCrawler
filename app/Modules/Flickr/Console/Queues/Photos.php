<?php

namespace App\Modules\Flickr\Console\Queues;

class Photos extends AbstractQueueCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:queues-photos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    protected function getJob(): string
    {
        return \App\Modules\Flickr\Jobs\Queues\Photos::class;
    }
}
