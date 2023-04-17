<?php

namespace App\Modules\Jav\Services\Movie\Traits;

use Carbon\Carbon;

trait HasDefaultMovie
{
    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    /**
     * DVD ID usually come with format likely: ABW-226
     *
     * @return string|null
     */
    public function getDvdId(): ?string
    {
        return $this->dvd_id ?? null;
    }

    /**
     * Content ID usually come with format likely: nkkvr00029
     *
     * @return string|null
     */
    public function getContentId(): ?string
    {
        return $this->content_id ?? null;
    }

    public function getGenres(): array
    {
        return $this->genres ?? [];
    }

    public function getPerformers(): array
    {
        return $this->performers ?? [];
    }


    public function getCover(): ?string
    {
        return $this->cover ?? null;
    }

    public function getSalesDate(): ?Carbon
    {
        return $this->sales_date ?? null;
    }

    public function getReleaseDate(): ?Carbon
    {
        return $this->release_date ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->description ?? null;
    }

    public function getTime(): ?int
    {
        return $this->time ?? null;
    }

    public function getDirector(): ?string
    {
        return $this->director ?? null;
    }

    public function getStudio(): ?string
    {
        return $this->studio ?? null;
    }

    public function getLabel(): ?string
    {
        return $this->label ?? null;
    }

    public function getChannels(): ?array
    {
        return $this->channels ?? null;
    }

    public function getSeries(): ?array
    {
        return $this->series ?? null;
    }

    public function getGallery(): ?array
    {
        return $this->gallery ?? null;
    }

    public function getImages(): ?array
    {
        return $this->images ?? null;
    }

    public function getSample(): ?array
    {
        return $this->sample ?? null;
    }
}
