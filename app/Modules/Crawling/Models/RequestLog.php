<?php

namespace App\Modules\Crawling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class RequestLog extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $fillable = [
        'url',
        'payload',
        'code',
        'response',
    ];

    protected $casts = [
        'url' => 'string',
        'payload' => 'array',
        'code' => 'integer',
        'response' => 'string',
    ];
}
