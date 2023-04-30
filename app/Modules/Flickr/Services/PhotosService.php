<?php

namespace App\Modules\Flickr\Services;

use App\Modules\Flickr\Repositories\PhotosRepository;
use Illuminate\Support\Collection;

class PhotosService
{
    public function __construct(private PhotosRepository $repository)
    {
    }

    public function insertBulk(Collection $items)
    {
        $this->repository->insertBulk($items);
    }
}
