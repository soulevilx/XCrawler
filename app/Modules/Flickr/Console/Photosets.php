<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Console\Command;

class Photosets extends Command
{
    protected $signature = 'flickr:photosets {nsid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch user\' photosets.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(FlickrService::class)
            ->photosets()
            ->getList(['user_id' => $this->argument('nsid')]);

        return 0;
    }
}
