<?php

namespace App\Modules\Jav\Tests\Unit\Services;

use App\Modules\Jav\Events\CreatingMovie;
use App\Modules\Jav\Events\CreatingMovieGenre;
use App\Modules\Jav\Events\CreatingMoviePerformer;
use App\Modules\Jav\Models\Genre;
use App\Modules\Jav\Models\Performer;
use App\Modules\Jav\Services\Movie\MovieService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    public function testCreate()
    {
        Event::fake([
            CreatingMovie::class,
            CreatingMovieGenre::class,
            CreatingMoviePerformer::class
        ]);
        $service = app(MovieService::class);
        $dvdId = $this->faker->uuid;
        $performers = [
            $this->faker->name,
            $this->faker->name,
        ];
        $genres = [
            $this->faker->word,
            $this->faker->word,
        ];
        $service->create([
            'dvd_id' => $dvdId,
            'performers' => $performers,
            'genres' => $genres,
        ]);

        $this->assertDatabaseHas('movies', [
            'dvd_id' => $dvdId,
        ]);

        $this->assertTrue(Genre::whereIn('name', $genres)->count() === 2);
        $this->assertTrue(Performer::whereIn('name', $performers)->count() === 2);

        Event::assertDispatched(CreatingMovie::class);
        Event::assertDispatched(CreatingMovieGenre::class);
        Event::assertDispatched(CreatingMoviePerformer::class);
    }
}
