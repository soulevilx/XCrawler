<?php

namespace App\Modules\Flickr\Services\Adapters\Traits;

use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\GetList;

trait HasList
{
    protected array $listFilter = [
        'per_page' => self::PER_PAGE,
    ];

    protected array $list = [];

    public function setFilter(string $key, string $value): self
    {
        $this->listFilter[$key] = $value;

        return $this;
    }

    public function getList(array $filter = []): void
    {
        GetList::dispatch(
            $this->getListMethod,
            self::LIST_ENTITIES,
            self::LIST_ENTITY,
            [...$this->listFilter, ...$filter]
        )->onQueue(PoolService::QUEUE_API);
    }
}
