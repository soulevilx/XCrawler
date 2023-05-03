<?php

namespace App\Modules\Flickr\Tests\Unit\Models;

use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use App\Modules\Flickr\Models\Photoset;
use App\Modules\Flickr\Tests\TestCase;

class PhotoTest extends TestCase
{
    public function testPhotosetsRelationship()
    {
        $contact = Contact::factory()->create();
        Photo::factory()->create([
            'id' => $this->faker->randomNumber(),
            'owner' => $contact->nsid,
        ]);
        $photo = Photo::factory()->create([
            'id' => $this->faker->randomNumber(),
            'owner' => $contact->nsid,
        ]);

        $photoset = Photoset::create([
            'id' => $this->faker->randomNumber(),
            'owner' => $contact->nsid,
        ]);

        $photo->photosets()->attach($photoset);

        $this->assertInstanceOf(Photoset::class, $photo->photosets->first());
        $this->assertInstanceOf(Photo::class, $photoset->photos->first());
    }
}
