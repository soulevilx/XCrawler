<?php

namespace App\Modules\Flickr\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Photoset extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'flickr_photosets';

    protected $guarded = [];
}
