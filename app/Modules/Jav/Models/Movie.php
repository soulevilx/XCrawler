<?php

namespace App\Modules\Jav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cover',
        'sales_date',
        'release_date',
        'content_id',
        'dvd_id',
        'description',
        'time',
        'director',
        'studio',
        'label',
        'channels',
        'series',
        'gallery',
        'images',
        'sample',
    ];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class)
            ->withTimestamps();
    }

    public function performers(): BelongsToMany
    {
        return $this->belongsToMany(Performer::class, 'performer_movie')
            ->withTimestamps();
    }
}
