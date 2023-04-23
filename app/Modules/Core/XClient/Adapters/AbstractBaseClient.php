<?php

namespace App\Modules\Core\XClient\Adapters;

use App\Modules\Core\XClient\XClient;

abstract class AbstractBaseClient implements XClientAdapterInterface
{
    public function __construct(protected XClient $client)
    {
        $this->client->setHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36',
        ]);

        $this->client->init([], [
            'stream' => false,
        ]);
    }
}
