<?php

namespace App\Modules\Jav\Events\Onejav;

use Illuminate\Queue\SerializesModels;

class OnejavItemParsed
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public \ArrayObject $item)
    {
    }
}
