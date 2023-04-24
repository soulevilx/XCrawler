<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class Favorites extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 500;
    protected string $getListMethod = 'flickr.favorites.getList';
    public const LIST_ENTITY = 'photo';
    public const LIST_ENTITIES = 'photos';
}
