<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class OAuthLog extends Model implements BaseModelInterface
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection = 'oauth_logs';

    protected $fillable = [
        'service',
        'path',
        'params',
        'method',
    ];

    protected $casts = [
        'service' => 'string',
        'path' => 'string',
        'params' => 'array',
        'method' => 'string',
    ];
}
