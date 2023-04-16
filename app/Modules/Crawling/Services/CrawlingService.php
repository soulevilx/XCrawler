<?php

namespace App\Modules\Crawling\Services;

use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;
use App\Modules\Crawling\Services\XClient\Adapters\DomClientAdapter;

class CrawlingService
{
    public function __construct(private DomClientAdapter $domClientAdapter)
    {
    }

    public function request(string $method, string $url, array $payload = []): XClientResponseInterface
    {
        return $this->domClientAdapter->request($method, $url, $payload);
    }
}
