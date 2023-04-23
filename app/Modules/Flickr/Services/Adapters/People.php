<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class People extends BaseAdapter
{
    use HasList;

    public const PER_PAGE = 500;

    protected string $getListMethod = 'flickr.people.getPhotos';
    protected string $listEntities = 'photos';
    protected string $listEntity = 'photo';

    public function getInfo(string $nsid): array
    {
        return $this->provider->request(
            'flickr.people.getInfo',
            ['user_id' => $nsid]
        )->getData()['person'];
    }
}
