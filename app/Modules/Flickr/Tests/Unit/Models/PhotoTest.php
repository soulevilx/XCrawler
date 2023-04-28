<?php

namespace App\Modules\Flickr\Tests\Unit\Models;

use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Tests\TestCase;

class PhotoTest extends TestCase
{
    public function testPhotosetsRelationship()
    {
        $photo = Photo::create([
            'id' => $this->faker->uuid,
            'owner' => $this->faker->uuid,
        ]);

        $photoset = Photoset::create([
            'id' => $this->faker->uuid,
        ]);

        $photo->photosets()->attach($photoset);

        $this->assertInstanceOf(Photoset::class, $photo->photosets->first());
        $this->assertInstanceOf(Photo::class, $photoset->photos->first());
    }
}
