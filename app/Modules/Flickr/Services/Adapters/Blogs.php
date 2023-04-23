<?php

namespace App\Modules\Flickr\Services\Adapters;

class Blogs extends BaseAdapter
{
    protected string $getListMethod = 'flickr.contacts.getList';
    protected string $listEntities = 'contacts';
    protected string $listEntity = 'contact';
}
