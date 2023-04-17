<?php

namespace App\Modules\Jav\Models;

use App\Modules\Jav\Services\Movie\MovieInterface;
use App\Modules\Jav\Services\Movie\Traits\HasDefaultMovie;
use App\Modules\Jav\Services\Movie\Traits\HasMovie;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onejav extends Model implements MovieInterface
{
    use HasFactory;
    use HasMovie;
    use HasDefaultMovie;

    protected $fillable = [
        'url',
        'cover',
        'dvd_id',
        'size',
        'date',
        'genres',
        'description',
        'performers',
        'torrent',
    ];

    protected $casts = [
        'url' => 'string',
        'cover' => 'string',
        'dvd_id' => 'string',
        'size' => 'float',
        'date' => 'date:Y-m-d',
        'genres' => 'array',
        'performers' => 'array',
        'description' => 'string',
        'torrent' => 'string',
    ];

    protected $table = 'onejav';
}
