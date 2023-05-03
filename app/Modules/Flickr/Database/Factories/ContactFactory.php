<?php

namespace App\Modules\Flickr\Database\Factories;

use App\Modules\Flickr\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ContactFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Contact::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => Str::orderedUuid(),
            'nsid' => $this->faker->uuid(),
            'username' => $this->faker->userName,
            'iconserver' => $this->faker->randomNumber(),
            'iconfarm' => $this->faker->randomNumber(),
            'ignored'=> $this->faker->boolean(),
            'rev_ignored'=> $this->faker->boolean(),
            'realname'=> $this->faker->name,
            'friend'=> $this->faker->boolean(),
            'family'=> $this->faker->boolean(),
            'path_alias'=> $this->faker->userName,
            'location'=> $this->faker->address,
        ];
    }
}

