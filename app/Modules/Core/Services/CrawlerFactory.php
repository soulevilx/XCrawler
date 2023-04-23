<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\XClient\Adapters\XClientAdapterInterface;

class CrawlerFactory
{
    public function make(XClientAdapterInterface $adapter)
    {
        return app()->makeWith(
            CrawlingService::class,
            ['adapter' => $adapter]
        );
    }
}
