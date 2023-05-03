<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Setting extends Model implements BaseModelInterface
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection = 'settings';

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    public function scopeKey($query, $key)
    {
        return $query->where('key', $key);
    }
}
