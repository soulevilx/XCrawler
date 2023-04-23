<?php

namespace App\Modules\Jav\Services\Movie;

use Carbon\Carbon;

interface MovieInterface
{
    public function getName(): ?string;

    public function getCover(): ?string;

    public function getSalesDate(): ?Carbon;

    public function getReleaseDate(): ?Carbon;

    /**
     * Content ID usually come with format likely: nkkvr00029
     */
    public function getContentId(): ?string;

    /**
     * DVD ID usually come with format likely: ABW-226
     */
    public function getDvdId(): ?string;

    public function getDescription(): ?string;

    public function getTime(): ?int;

    public function getDirector(): ?string;

    public function getStudio(): ?string;

    public function getLabel(): ?string;

    public function getChannels(): ?array;

    public function getSeries(): ?array;

    public function getGallery(): ?array;

    public function getImages(): ?array;

    public function getSample(): ?array;

    public function getGenres(): array;

    public function getPerformers(): array;
}
