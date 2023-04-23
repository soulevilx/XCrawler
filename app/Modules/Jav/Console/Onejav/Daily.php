<?php

namespace App\Modules\Jav\Console\Onejav;

use App\Modules\Jav\Jobs\OnejavDaily;
use Illuminate\Console\Command;

class Daily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'jav:onejav-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Onejav daily.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        OnejavDaily::dispatch()
            ->onQueue('low');

        return 0;
    }
}
