<?php

namespace App\Modules\Core\Services\Traits;

use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;
use App\Modules\Crawling\Models\RequestLog;

trait HasRequestLog
{
    public function logRequest(
        XClientResponseInterface $response,
        string $url,
        array $payload = []
    ) {
        RequestLog::create(
            [
                'url' => $url,
                'payload' => $payload,
                'response' => (string) $response->getRaw(),
                'status' => $response->getStatusCode(),
                'success' => $response->isSuccess()
            ]
        );
    }
}
