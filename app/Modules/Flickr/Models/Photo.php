<?php

namespace App\Modules\Flickr\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use App\Modules\Core\Models\Traits\HasUuid;
use App\Modules\Flickr\Database\Factories\PhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Photo extends Model implements BaseModelInterface
{
    use HasFactory;
    use HasUuid;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $table = 'flickr_photos';
    protected $fillable = [
        'uuid',
        'id',
        'owner',
        'secret',
        'server',
        'farm',
        'title',
        'ispublic',
        'isfriend',
        'isfamily',
    ];

    protected $casts = [
        'uuid' => 'string',
        'id' => 'integer',
        'owner' => 'string',
        'secret' => 'string',
        'server' => 'integer',
        'farm' => 'integer',
        'title' => 'string',
        'ispublic' => 'boolean',
        'isfriend' => 'boolean',
        'isfamily' => 'boolean',
    ];

    protected static function newFactory()
    {
        return PhotoFactory::new();
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'owner', 'nsid');
    }

    public function photosets()
    {
        return $this->belongsToMany(Photoset::class, 'photo_photoset', 'photo_id', 'photoset_id');
    }
}
