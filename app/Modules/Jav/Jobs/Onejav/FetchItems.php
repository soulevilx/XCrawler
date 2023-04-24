<?php

namespace App\Modules\Jav\Jobs\Onejav;

use App\Modules\Core\Jobs\AbstractLowQueue;
use App\Modules\Jav\Crawlers\OnejavCrawler;

class FetchItems extends AbstractLowQueue
{
    public function __construct(public string $endpoint, public array $payload = [])
    {
        parent::__construct();
        $this->payload['page'] = $this->payload['page'] ?? 1;
    }

    public function handle()
    {
        $crawler = app(OnejavCrawler::class);
        $crawler->items($this->endpoint, $this->payload);

        if ($this->payload['page'] < $crawler->lastPage()) {
            self::dispatch($this->endpoint, ['page' => $this->payload['page'] + 1]);
        }
    }
}
