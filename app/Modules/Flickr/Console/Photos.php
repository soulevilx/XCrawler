<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Console\Command;

class Photos extends Command
{
    protected $signature = 'flickr:photos {nsid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch user\' photos .';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(FlickrService::class)
            ->people()
            ->getList(['user_id' => $this->argument('nsid')]);

        return 0;
    }
}
