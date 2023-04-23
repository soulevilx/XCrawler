<?php

namespace App\Modules\Jav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Performer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'name' => 'string',
    ];
}
