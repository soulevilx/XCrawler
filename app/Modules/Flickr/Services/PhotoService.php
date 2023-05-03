<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Flickr\Repositories\PhotoRepository;
use Illuminate\Support\Collection;

class PhotoService
{
    public function __construct(private PhotoRepository $repository)
    {
    }

    public function insert(Collection $items)
    {
        $this->repository->insert($items);
        /**
         * Register pool
         * - Photosizes
         * - Photosets
         */
    }
}
