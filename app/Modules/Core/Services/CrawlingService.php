<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Events\CrawlingFailed;
use App\Modules\Core\Events\CrawlingSuccess;
use App\Modules\Core\Services\Traits\HasRequestLog;
use App\Modules\Core\XClient\Adapters\XClientAdapterInterface;
use App\Modules\Core\XClient\Response\XClientResponseInterface;
use Illuminate\Support\Facades\Event;

class CrawlingService
{
    use HasRequestLog;

    public function __construct(private readonly XClientAdapterInterface $adapter)
    {
    }

    public function request(string $method, string $url, array $payload = [], array $options = []): XClientResponseInterface
    {
        $response = $this->adapter->request($method, $url, $payload, $options);

        if ($response->isSuccess()) {
            Event::dispatch(new CrawlingSuccess());
        } else {
            Event::dispatch(new CrawlingFailed());
        }

        $this->logRequest($response, $method, $url, $payload);

        return $response;
    }
}
