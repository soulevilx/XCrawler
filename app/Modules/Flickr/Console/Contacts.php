<?php

namespace App\Modules\Flickr\Console;

use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Console\Command;

class Contacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        app(FlickrService::class)->contacts()->getList();
    }
}
