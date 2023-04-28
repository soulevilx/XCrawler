<?php

namespace App\Modules\Flickr\Console\Downloads;

use App\Modules\Core\Models\Download;
use Illuminate\Console\Command;

class Photoset extends Command
{
    protected $signature = 'flickr:downloads-photoset {nsid}';

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
        $nsid = $this->argument('nsid');

        Download::create([
            'type' => 'photoset',
            'nsid' => $nsid,
            'state_code' => Download::STATE_CODE_PENDING,
        ]);
    }
}
