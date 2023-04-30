<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\GetList;
use App\Modules\Flickr\Repositories\PhotosetsRepository;
use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class PhotoSets extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 500;

    protected string $getListMethod = 'flickr.photosets.getList';
    public const LIST_ENTITY = 'photoset';
    public const LIST_ENTITIES = 'photosets';

    private PhotosetsRepository $repository;

    public function __construct()
    {
        parent::__construct();

        $this->repository = app(PhotosetsRepository::class);
    }

    public function getPhotos(array $filter = [])
    {
        GetList::dispatch(
            'flickr.photosets.getPhotos',
            'photoset',
            'photo',
            [...$this->listFilter, ...$filter]
        )->onQueue(PoolService::QUEUE_API);
    }
}
