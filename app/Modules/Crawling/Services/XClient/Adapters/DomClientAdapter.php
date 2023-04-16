<?php

namespace App\Modules\Crawling\Services\XClient\Adapters;

use App\Modules\Core\Services\XClient\Adapters\XClientAdapterInterface;
use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;
use App\Modules\Core\Services\XClient\XClient;
use App\Modules\Crawling\Events\CrawlingFailed;
use App\Modules\Crawling\Events\CrawlingSuccess;
use App\Modules\Crawling\Services\XClient\Response\DomResponse;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Event;

class DomClientAdapter implements XClientAdapterInterface
{
    private DomResponse $response;

    public function request(string $method, string $url, array $payload = []): XClientResponseInterface
    {
        $client = app(XClient::class);
        $client->init([], [
            'stream' => false,
        ]);

        $client->setHeaders([
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36'
        ]);

        try {
            $this->response = new DomResponse($client->request($url, $payload, $method));

        } catch (ClientException $e) {
            $this->response = new DomResponse($e->getResponse());
        } finally {
            if (!isset($this->response)) {
                $this->response = new DomResponse(null);
            }
            if ($this->response->isSuccess()) {
                Event::dispatch(new CrawlingSuccess());
            } else {
                Event::dispatch(new CrawlingFailed());
            }
            return $this->response;
        }
    }
}
