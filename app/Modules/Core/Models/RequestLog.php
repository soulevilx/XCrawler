<?php

namespace App\Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class RequestLog extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    public const TABLE_NAME = 'request_logs';

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
