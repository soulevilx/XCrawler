<?php

namespace App\Modules\Jav\Services\Movie;

use App\Modules\Jav\Events\CreatingMovie;
use App\Modules\Jav\Events\CreatingMovieGenre;
use App\Modules\Jav\Events\CreatingMoviePerformer;
use App\Modules\Jav\Models\Genre;
use App\Modules\Jav\Models\Performer;
use App\Modules\Jav\Repositories\MovieRepository;
use Illuminate\Support\Facades\Event;

class MovieService
{
    public function create(array $attributes)
    {
        Event::dispatch(new CreatingMovie());
        $movie = app(MovieRepository::class)->create($attributes);

        if (! empty($attributes['genres'])) {
            Event::dispatch(new CreatingMovieGenre());
            foreach ($attributes['genres'] as $genre) {
                $movie->genres()->attach(Genre::firstOrCreate(['name' => $genre]));
            }
        }

        if (! empty($attributes['performers'])) {
            Event::dispatch(new CreatingMoviePerformer());
            foreach ($attributes['performers'] as $performer) {
                $movie->performers()->attach(Performer::firstOrCreate(['name' => $performer]));
            }
        }
    }
}
