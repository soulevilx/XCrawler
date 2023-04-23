<?php

namespace App\Modules\Flickr\Services\Adapters;

class PhotosPeople extends BaseAdapter
{
    protected string $getListMethod = 'flickr.photos.people.getList';
    protected string $listEntities = 'people';
    protected string $listEntity = 'person';
}
