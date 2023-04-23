<?php

namespace App\Modules\Core\Services\Traits;

use App\Modules\Core\Models\RequestLog;
use App\Modules\Core\XClient\Response\XClientResponseInterface;

trait HasRequestLog
{
    public function logRequest(
        XClientResponseInterface $response,
        string $method,
        string $url,
        array $payload = []
    ) {
        RequestLog::create(
            [
                'method' => $method,
                'url' => $url,
                'payload' => $payload,
                'response' => (string) $response->getRaw(),
                'status' => $response->getStatusCode(),
                'success' => $response->isSuccess(),
            ]
        );
    }
}
