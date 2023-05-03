<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Jenssegers\Mongodb\Eloquent\Model;

class Download extends Model implements BaseModelInterface
{
    protected $connection = 'mongodb';

    protected $collection = 'downloads';

    protected $guarded = [];

    public const STATE_CODE_PENDING = 'pending';
}
