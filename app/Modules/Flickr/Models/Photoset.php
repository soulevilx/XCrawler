<?php

namespace App\Modules\Flickr\Models;

use App\Modules\Core\Models\Interfaces\BaseModelInterface;
use App\Modules\Flickr\Database\factories\PhotosetFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Photoset extends Model implements BaseModelInterface
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $table = 'flickr_photosets';

    protected $fillable = [
        'uuid',
        'id',
        'owner',
        'primary',
        'secret',
        'server',
        'farm',
        'title',
        'description',
        'count_photos',
        'count_videos',
    ];
    protected $casts = [
        'uuid' => 'string',
        'id' => 'integer',
        'owner' => 'string',
        'primary' => 'string',
        'secret' => 'string',
        'server' => 'integer',
        'farm' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'count_photos' => 'integer',
        'count_videos' => 'integer',
    ];

    protected static function newFactory()
    {
        return PhotosetFactory::new();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'owner', 'nsid');
    }

    public function photos(): BelongsToMany
    {
        return $this->belongsToMany(Photo::class, 'photo_photoset', 'photoset_id', 'photo_id');
    }
}
