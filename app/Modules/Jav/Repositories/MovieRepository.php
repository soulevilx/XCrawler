<?php

namespace App\Modules\Jav\Repositories;

use App\Modules\Jav\Models\Movie;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\LazyCollection;

class MovieRepository
{
    public function __construct(public Movie $model)
    {
    }

    public function uniqueColumns(): array
    {
        return ['content_id', 'dvd_id'];
    }

    protected function getColumns()
    {
        return Schema::getColumnListing($this->model->getTable());
    }

    public function create(array $attributes): Model
    {
        return $this->model->updateOrCreate(
            array_intersect_key($attributes, array_flip($this->uniqueColumns())),
            Arr::only($attributes, $this->getColumns())
        );
    }

    public function getAll(): LazyCollection
    {
        return $this->model->cursor();
    }

    public function count(): int
    {
        return $this->model->count();
    }
}
