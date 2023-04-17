<?php

namespace App\Modules\Jav\Services;

use App\Modules\Crawling\Services\Crawlers\CrawlerAdapterInterface;
use Illuminate\Support\Collection;

class JavClient
{
    public function __construct(
        private readonly CrawlerAdapterInterface $crawler
    ) {
    }

    public function crawl(string $method, string $url, array $data = []): Collection
    {
        return $this->crawler->{$method}($url, $data);
    }

    public function crawlWithRecursive(string $method, string $url, array $data = [], &$pages = null): Collection
    {
        $items = collect();
        $pages = $this->crawler->{$method}($items, $url, $data);

        return $items;
    }
}
