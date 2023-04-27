<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Photos as PhotosJob;
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
        PhotosJob::dispatch($this->argument('nsid'))->onQueue(PoolService::QUEUE_API);
    }
}
