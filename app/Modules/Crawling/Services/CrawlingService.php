<?php

namespace App\Modules\Crawling\Services;

use App\Modules\Core\Services\XClient\Adapters\XClientAdapterInterface;
use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;

class CrawlingService
{
    public function __construct(private readonly XClientAdapterInterface $adapter)
    {
    }

    public function request(string $method, string $url, array $payload = []): XClientResponseInterface
    {
        return $this->adapter->request($method, $url, $payload);
    }
}
