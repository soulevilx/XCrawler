<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Events\CreatedBulkOfPhotosets;
use App\Modules\Flickr\Jobs\GetList;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class PhotoSets extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 500;

    protected string $getListMethod = 'flickr.photosets.getList';
    public const LIST_ENTITY = 'photoset';
    public const LIST_ENTITIES = 'photosets';

    public function getPhotos(array $filter = [])
    {
        GetList::dispatch(
            'flickr.photosets.getPhotos',
            self::LIST_ENTITIES,
            self::LIST_ENTITY,
            [...$this->listFilter, ...$filter]
        )->onQueue(PoolService::QUEUE_API);
    }

    public function createMany(Collection $items): void
    {
        $existsPhotosets = Photoset::whereIn('id', $items->pluck('id'))->pluck('id')->toArray();
        $notExistsPhotosets = $items->filter(
            fn($item) => !in_array($item['id'], $existsPhotosets)
        )->values()->all();

        if (!empty($notExistsPhotosets)) {
            Photoset::insert($notExistsPhotosets);

            Event::dispatch(new CreatedBulkOfPhotosets($notExistsPhotosets));
        }
    }
}
