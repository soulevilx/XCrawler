<?php

namespace App\Modules\Core\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

abstract class AbstractBaseRepository
{
    protected function getColumns()
    {
        return Schema::getColumnListing($this->model->getTable());
    }

    public function insert(Collection $items): bool
    {
        if ($items->isEmpty()) {
            return false;
        }

        $this->model->insert($items->toArray());

        return true;
    }

    public function getItems(array|Collection $filter): EloquentCollection
    {
        if (is_array($filter)) {
            $filter = collect($filter);
        }
        return $this->buildIndexQuery($filter)->get();
    }

    private function buildIndexQuery(Collection $request): Builder
    {
        $baseQuery = $this->model->newQuery();

        $orderBy = $request->get('orderBy', 'created_at');
        $orderDirection = $request->get('orderDirection', 'desc');

        $baseQuery->orderBy($orderBy, $orderDirection);

        $baseQuery = $this->buildConditions($request, $baseQuery);

        if ($request->has('limit')) {
            $baseQuery->limit($request->get('limit'));
        }

        return $baseQuery;
    }

    private function buildConditions(Collection $request, Builder $builder): Builder
    {
        if ($request->has('whereAfter')) {
            foreach ($request->get('whereAfter') as $key => $values) {
                $builder->where(function ($query) use ($key, $values) {
                    $query->where($key, '<=', $values)
                        ->orWhereNull($key);
                });
            }
        }

        if ($where = $request->get('where')) {
            if (is_array($where)) {
                foreach ($where as $key => $value) {
                    $builder->where($key, $value);
                }
            } else {
                $builder->where($request->get('where'));
            }
        }

        if ($request->has('whereIn')) {
            foreach ($request->get('whereIn') as $key => $values) {
                $builder->whereIn($key, $values);
            }
        }

        if (is_array($request->get('whereNull'))) {
            foreach ($request->get('whereNull') as $column) {
                $builder->whereNull($column);
            }
        }

        if (is_array($request->has('whereNotNull'))) {
            foreach ($request->get('whereNotNull') as $column) {
                $builder->whereNotNull($column);
            }
        }

        if (is_array($request->has('whereNotEmpty'))) {
            foreach ($request->get('whereNotEmpty') as $column) {
                $builder->where($column, '<>', '');
            }
        }

        if (is_array($request->has('whereHas'))) {
            foreach ($request->get('whereHas') as $relationship => $where) {
                $builder->whereHas($relationship, function ($query) use ($where) {
                    $query->where($where);
                });
            }
        }

        if (is_array($request->get('whereDoesntHave'))) {
            foreach ($request->get('whereDoesntHave') as $relationship => $where) {
                $builder->whereDoesntHave($relationship, function ($query) use ($where) {
                    $query->where($where);
                });
            }
        }

        if ($request->has('has')) {
            foreach ((array) $request->get('has') as $relationship) {
                $builder->has($relationship);
            }
        }

        if ($request->has('doesntHave')) {
            foreach ((array) $request->get('doesntHave') as $relationship) {
                $builder->doesntHave($relationship);
            }
        }

        if ($request->has('search') && $request->has('searchIn')) {
            $builder->where(function ($query) use ($request) {
                foreach ($request->get('searchIn') as $key => $columns) {
                    if (is_array($columns)) {
                        $query->orWhereHas($key, function ($query) use ($request, $columns) {
                            $query->where(function ($query) use ($request, $columns) {
                                foreach ($columns as $column) {
                                    $query->orWhere($column, 'like', '%'.$request->input('search').'%');
                                }
                            });
                        });
                    } else {
                        $query->orWhere($columns, 'like', '%'.$request->input('search').'%');
                    }
                }
            });
        }

        if ($request->has('addSelect')) {
            foreach ((array) $request->get('addSelect') as $addSelect) {
                $builder->addSelect($addSelect);
            }
        }

        if ($groupsBy = $request->get('groupBy')) {
            if (is_array($groupsBy)) {
                foreach ($groupsBy as $groupBy) {
                    $builder->groupBy($groupBy);
                }
            } else {
                $builder->groupBy($groupsBy);
            }
        }

        return $builder;
    }
}
