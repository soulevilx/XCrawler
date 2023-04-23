<?php

namespace App\Modules\Flickr\Services\Adapters\Traits;

trait HasList
{
    protected array $listFilter = [
        'per_page' => self::PER_PAGE,
    ];

    protected array $list = [];

    public function getList(array $filter = [])
    {
        $data = $this->provider->request(
            $this->getListMethod,
            [...$this->listFilter, ...$filter]
        )->getData();

        if (!isset($data[$this->listEntities])) {
            return [];
        }

        $this->list = [...$this->list, ...$data[$this->listEntities][$this->listEntity]];

            $page = $data[$this->listEntities]['page'];
            $pages = $data[$this->listEntities]['pages'];

        if ($page < $pages) {
            sleep(1);
            $this->getList(
                [...$filter, 'page' => $page + 1]
            );
        }


        return $this->list;
    }
}
