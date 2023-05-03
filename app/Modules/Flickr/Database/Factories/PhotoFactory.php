<?php

namespace App\Modules\Flickr\Database\Factories;

use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::orderedUuid(),
            'id' => $this->faker->randomNumber(),
            'owner' => Contact::factory()->create()->nsid,
            'secret' => $this->faker->word,
            'server' => $this->faker->randomNumber(),
            'farm' => $this->faker->randomNumber(),
            'title' => $this->faker->word,
            'ispublic' => $this->faker->boolean,
            'isfriend' => $this->faker->boolean,
            'isfamily' => $this->faker->boolean,
        ];
    }
}

