<?php

namespace App\Modules\Flickr\Services\Adapters\Interfaces;

interface ListInterface extends AdapterInterface
{
    public function getList(array $filter = []): void;
}
