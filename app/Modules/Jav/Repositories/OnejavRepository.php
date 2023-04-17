<?php

namespace App\Modules\Jav\Repositories;

use App\Modules\Jav\Models\Onejav;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\LazyCollection;


class OnejavRepository
{
    public function __construct(public Onejav $model)
    {
    }

    public function uniqueColumns(): array
    {
        return ['url'];
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
