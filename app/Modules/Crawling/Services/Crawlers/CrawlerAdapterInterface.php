<?php

namespace App\Modules\Crawling\Services\Crawlers;

interface CrawlerAdapterInterface
{
    public function items(string $url, array $data = []);
}
