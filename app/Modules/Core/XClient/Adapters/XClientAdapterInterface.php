<?php

namespace App\Modules\Core\XClient\Adapters;

use App\Modules\Core\XClient\Response\XClientResponseInterface;

interface XClientAdapterInterface
{
    public function request(
        string $method,
        string $url,
        array $payload = [],
        array $options = []
    ): XClientResponseInterface;
}
