<?php

namespace App\Modules\Crawling\Services\Crawlers;

use App\Modules\Crawling\Services\CrawlingService;
use App\Modules\Crawling\Services\XClient\Adapters\DomClientAdapter;

abstract class AbstractDomCrawlerAdapter implements CrawlerAdapterInterface
{
    protected CrawlingService $service;

    public function __construct()
    {
        $this->service = app()->makeWith(CrawlingService::class, [
            'adapter' => app(DomClientAdapter::class)
        ]);
    }
}
