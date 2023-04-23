<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class Favorites extends BaseAdapter
{
    use HasList;

    public const PER_PAGE = 500;
    protected string $getListMethod = 'flickr.favorites.getList';
    protected string $listEntities = 'photos';
    protected string $listEntity = 'photo';
}
