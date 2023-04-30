<?php

namespace App\Modules\Flickr\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\BelongsToMany;

class Photo extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'flickr_photos';

    protected $guarded = [];

    public function scopeByNsid(Builder $builder, string $nsid)
    {
        return $builder->where(compact('nsid'));
    }

    public function photosets(): BelongsToMany
    {
        return $this->belongsToMany(Photoset::class, 'photo_photosets', 'photo_ids', 'photoset_ids');
    }
}
