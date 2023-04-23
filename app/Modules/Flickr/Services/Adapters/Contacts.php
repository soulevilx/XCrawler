<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class Contacts extends BaseAdapter
{
    use HasList;

    public const PER_PAGE = 1000;

    protected string $getListMethod = 'flickr.contacts.getList';
    protected string $listEntities = 'contacts';
    protected string $listEntity = 'contact';

    public const ERROR_CODE_INVALID_SORT_PARAMETER = 1;
}
