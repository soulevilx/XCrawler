<?php

namespace App\Modules\Jav\Console\Onejav;

use App\Modules\Jav\Jobs\OnejavAll;
use Illuminate\Console\Command;

class All extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'jav:onejav-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Onejav all.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        OnejavAll::dispatch()
            ->onQueue('low');

        return 0;
    }
}
