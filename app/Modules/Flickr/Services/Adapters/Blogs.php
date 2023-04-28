<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class Blogs extends BaseAdapter implements ListInterface
{
    use HasList;

    protected string $getListMethod = 'flickr.blogs.getList';
    protected string $listEntities = 'blogs';
    protected string $listEntity = 'blog';
}
