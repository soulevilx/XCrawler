<?php

namespace App\Modules\Core\Repositories;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class PoolRepository extends AbstractBaseRepository
{
    public function __construct(protected Pool $model)
    {
    }

    public function getItems(array|Collection $filter = []): EloquentCollection
    {
        $filter = is_array($filter) ? collect($filter) : $filter;

        if (!$filter->has('limit')) {
            $filter->merge([
                'limit' => config('core.pool.limit'),
            ]);
        }

        if (!$filter->has('state_code')) {
            $filter->merge([
                'state_code' => PoolService::STATE_CODE_INIT,
            ]);
        }

        if ($filter->has('job')) {
            $filter->merge([
                'where' => [
                    'job' => $filter->get('job'),
                ],
            ]);
        }

        return parent::getItems($filter);
    }
}
