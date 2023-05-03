<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class RequestLog extends Model implements BaseModelInterface
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection = 'request_logs';

    protected $fillable = [
        'url',
        'payload',
        'response',
        'status',
        'success',
    ];

    protected $casts = [
        'url' => 'string',
        'payload' => 'array',
        'response' => 'string',
        'status' => 'integer',
        'success' => 'boolean',
    ];
}
