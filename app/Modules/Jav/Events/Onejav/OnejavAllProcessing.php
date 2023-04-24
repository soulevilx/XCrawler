<?php

namespace App\Modules\Jav\Events\Onejav;

class OnejavAllProcessing
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public int $currentPage)
    {
        //
    }
}
