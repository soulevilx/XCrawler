<?php

namespace App\Modules\Flickr\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Contact extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'flickr_contacts';

    protected $guarded = [];

    public function scopeByNsid(Builder $builder, string $nsid)
    {
        return $builder->where(compact('nsid'));
    }
}
