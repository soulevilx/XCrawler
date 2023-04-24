<?php

namespace App\Modules\Core\XClient\Adapters;

use App\Modules\Core\XClient\Response\DomResponse;
use App\Modules\Core\XClient\Response\XClientResponseInterface;
use GuzzleHttp\Exception\ClientException;

class DomClientAdapter extends AbstractBaseClient
{
    public function request(
        string $method,
        string $url,
        array $payload = [],
        array $options = []
    ): XClientResponseInterface {
        try {
            $response = new DomResponse($this->client->request($url, $payload, $method));
        } catch (ClientException $e) {
            $response = new DomResponse($e->getResponse());
        } finally {

            if (!isset($response)) {
                $response = new DomResponse(null);
            }

            return $response;
        }
    }
}
