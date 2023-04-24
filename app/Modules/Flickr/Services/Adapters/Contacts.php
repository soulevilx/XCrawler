<?php

namespace App\Modules\Flickr\Services\Adapters;

use App\Modules\Flickr\Services\Adapters\Interfaces\ListInterface;
use App\Modules\Flickr\Services\Adapters\Traits\HasList;

class Contacts extends BaseAdapter implements ListInterface
{
    use HasList;

    public const PER_PAGE = 1000;
    public const LIST_ENTITY = 'contact';
    public const LIST_ENTITIES = 'contacts';

    protected string $getListMethod = 'flickr.contacts.getList';

    public const ERROR_CODE_INVALID_SORT_PARAMETER = 1;
}
