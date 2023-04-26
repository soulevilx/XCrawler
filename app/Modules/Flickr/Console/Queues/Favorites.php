<?php

namespace App\Modules\Flickr\Console\Queues;

class Favorites extends AbstractQueueCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:queues-favorites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get favorites photos from Flickr';

    protected function getJob(): string
    {
        return \App\Modules\Flickr\Jobs\Queues\Favorites::class;
    }
}
