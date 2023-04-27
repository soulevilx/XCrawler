<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Favorites as FavoritesJob;
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
        FavoritesJob::dispatch($this->argument('nsid'))->onQueue(PoolService::QUEUE_API);
    }
}
