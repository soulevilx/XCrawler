<?php

namespace App\Modules\Core\Services\XClient\Adapters;

use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;

interface XClientAdapterInterface
{
    public function request(string $method, string $url, array $payload = []): XClientResponseInterface;
}
