<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class People extends BaseAdapter implements ListInterface
{
    use HasList;
    public const PER_PAGE = 500;

    protected string $getListMethod = 'flickr.people.getPhotos';
    public const LIST_ENTITY = 'photo';
    public const LIST_ENTITIES = 'photos';


    public function getInfo(string $nsid): array
    {
        return $this->provider->request(
            'flickr.people.getInfo',
            ['user_id' => $nsid]
        )->getData()['person'];
    }
}
