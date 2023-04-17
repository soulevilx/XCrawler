<?php

namespace App\Modules\Jav\Services\Movie\Observers;

use App\Modules\Jav\Services\Movie\MovieInterface;
use App\Modules\Jav\Services\Movie\MovieService;

class MovieObserver
{
    public function __construct()
    {
    }

    /**
     * Handle created event.
     *
     * @return void
     */
    public function created(MovieInterface $model)
    {
        app(MovieService::class)->create([
            'name' => $model->getName(),
            'cover' => $model->getCover(),
            'sales_date' => $model->getSalesDate(),
            'release_date' => $model->getReleaseDate(),
            'content_id' => $model->getContentId(),
            'dvd_id' => $model->getDvdId(),
            'description' => $model->getDescription(),
            'time' => $model->getTime(),
            'director' => $model->getDirector(),
            'studio' => $model->getStudio(),
            'label' => $model->getLabel(),
            'channels' => $model->getChannels(),
            'series' => $model->getSeries(),
            'gallery' => $model->getGallery(),
            'images' => $model->getImages(),
            'sample' => $model->getSample(),
            'genres' => $model->getGenres(),
            'performers' => $model->getPerformers(),
        ]);
    }

    public function updated(MovieInterface $model)
    {
    }
}
