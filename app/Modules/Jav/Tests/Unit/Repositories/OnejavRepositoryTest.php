<?php

namespace App\Modules\Jav\Tests\Unit\Repositories;

use App\Modules\Jav\Events\CreatingMovie;
use App\Modules\Jav\Events\CreatingMovieGenre;
use App\Modules\Jav\Events\CreatingMoviePerformer;
use App\Modules\Jav\Models\Genre;
use App\Modules\Jav\Models\Performer;
use App\Modules\Jav\Repositories\OnejavRepository;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OnejavRepositoryTest extends TestCase
{
    public function testCreateFully()
    {
        Event::fake([
            CreatingMovie::class,
            CreatingMovieGenre::class,
            CreatingMoviePerformer::class,
        ]);
        $repository = app(OnejavRepository::class);
        $performers = [
            $this->faker->name,
            $this->faker->name,
        ];
        $genres = [
            $this->faker->word,
            $this->faker->word,
        ];
        $model = $repository->create([
            'torrent' => $this->faker->url,
            'url' => $this->faker->url,
            'dvd_id' => $this->faker->uuid,
            'performers' => $performers,
            'genres' => $genres,
        ]);

        $this->assertDatabaseHas('onejav', [
            'url' => $model->url,
            'torrent' => $model->torrent,
        ]);

        $this->assertTrue(Genre::whereIn('name', $genres)->count() === 2);
        $this->assertTrue(Performer::whereIn('name', $performers)->count() === 2);

        Event::assertDispatched(CreatingMovie::class);
        Event::assertDispatched(CreatingMovieGenre::class);
        Event::assertDispatched(CreatingMoviePerformer::class);
    }

    public function testCreateWithoutGenre()
    {
        Event::fake([
            CreatingMovie::class,
            CreatingMovieGenre::class,
            CreatingMoviePerformer::class,
        ]);
        $repository = app(OnejavRepository::class);
        $performers = [
            $this->faker->name,
            $this->faker->name,
        ];

        $model = $repository->create([
            'torrent' => $this->faker->url,
            'url' => $this->faker->url,
            'dvd_id' => $this->faker->uuid,
            'performers' => $performers,

        ]);

        $this->assertDatabaseHas('onejav', [
            'url' => $model->url,
            'torrent' => $model->torrent,
        ]);

        $this->assertDatabaseCount('genres', 0);
        $this->assertTrue(Performer::whereIn('name', $performers)->count() === 2);

        Event::assertDispatched(CreatingMovie::class);
        Event::assertNotDispatched(CreatingMovieGenre::class);
        Event::assertDispatched(CreatingMoviePerformer::class);
    }

    public function testCreateWithoutPerformer()
    {
        Event::fake([
            CreatingMovie::class,
            CreatingMovieGenre::class,
            CreatingMoviePerformer::class,
        ]);
        $repository = app(OnejavRepository::class);

        $model = $repository->create([
            'torrent' => $this->faker->url,
            'url' => $this->faker->url,
            'dvd_id' => $this->faker->uuid,
        ]);

        $this->assertDatabaseHas('onejav', [
            'url' => $model->url,
            'torrent' => $model->torrent,
        ]);

        $this->assertDatabaseCount('genres', 0);
        $this->assertDatabaseCount('performers', 0);

        Event::assertDispatched(CreatingMovie::class);
        Event::assertNotDispatched(CreatingMovieGenre::class);
        Event::assertNotDispatched(CreatingMoviePerformer::class);
    }
}
