<?php

namespace App\Modules\Flickr\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use App\Modules\Core\Models\Traits\HasUuid;
use App\Modules\Flickr\Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model implements BaseModelInterface
{
    use HasFactory;
    use HasUuid;

    protected $primaryKey = 'nsid';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'flickr_contacts';

    protected $fillable = [
        'uuid',
        'nsid',
        'username',
        'iconserver',
        'iconfarm',
        'ignored',
        'rev_ignored',
        'realname',
        'friend',
        'family',
        'path_alias',
        'location',
        'ispro',
        'is_deleted',
        'details',
    ];

    protected $casts = [
        'uuid' => 'string',
        'nsid' => 'string',
        'username' => 'string',
        'iconserver' => 'integer',
        'iconfarm' => 'integer',
        'ignored' => 'boolean',
        'rev_ignored' => 'boolean',
        'realname' => 'string',
        'friend' => 'boolean',
        'family' => 'boolean',
        'path_alias' => 'string',
        'location' => 'string',
        'ispro' => 'boolean',
        'is_deleted' => 'boolean',
        'details' => 'array',
    ];

    protected static function newFactory()
    {
        return ContactFactory::new();
    }
    public function scopeByNsid($query, string $nsid)
    {
        return $query->where('nsid', $nsid);
    }
    public function scopeByNsids($query, array $nsids)
    {
        return $query->whereIn('nsid', $nsids);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'owner', 'nsid');
    }
}
