<?php

namespace App\Modules\Jav\Listeners;

use App\Modules\Jav\Events\OnejavItemParsed;
use App\Modules\Jav\Services\OnejavService;

class OnejavSubscriber
{
    public function onOnejavItemParsed(OnejavItemParsed $event)
    {
        app(OnejavService::class)->create($event->item->getArrayCopy());
    }

    public function subscribe($events)
    {
        return [
            OnejavItemParsed::class => 'onOnejavItemParsed',
        ];
    }
}
