<?php

namespace App\Modules\Flickr\Database\Factories;

use App\Modules\Flickr\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhotosetFactory extends Factory
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

        ];
    }
}

