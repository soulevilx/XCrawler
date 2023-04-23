<?php

namespace App\Modules\Jav\Services\Movie\Traits;

use App\Modules\Jav\Services\Movie\Observers\MovieObserver;

trait HasMovie
{
    public static function bootHasMovie()
    {
        static::observe(MovieObserver::class);
    }

    public function initializeHasMovie()
    {
        $this->mergeFillable(['dvd_id']);
        $this->mergeCasts(['dvd_id' => 'string']);
    }
}
