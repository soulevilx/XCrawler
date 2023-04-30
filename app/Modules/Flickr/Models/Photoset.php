<?php

namespace App\Modules\Flickr\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Relations\BelongsToMany;

class Photoset extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'flickr_photosets';

    protected $guarded = [];

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(
            Photo::class,
            'photo_photosets',
            'photoset_ids',
            'photo_ids'
        );
    }
}
