<?php

namespace App\Modules\Core\Models;

use Jenssegers\Mongodb\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;

class Download extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'downloads';

    protected $guarded = [];

    public const STATE_CODE_PENDING = 'pending';
}
