<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Console\Command;

class Favorites extends Command
{
    protected $signature = 'flickr:favorites {nsid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch user\' favorites photos .';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(FlickrService::class)
            ->favorites()
            ->getList(['user_id' => $this->argument('nsid')]);

        return 0;
    }
}
