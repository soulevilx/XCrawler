<?php

namespace App\Modules\Flickr\Services\Adapters;

interface FlickrAdapterInterface
{
    public function endpoint(string $method): string;

    public function getDefaults(): array;
}
