<?php

namespace App\Modules\Flickr\Events;

class FetchedFlickrItems
{
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public array $data, public string $listEntities, public string $listEntity)
    {
        //
    }
}
