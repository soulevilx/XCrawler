<?php

namespace App\Modules\Core\XClient\Adapters;

use App\Modules\Core\XClient\Response\BaseResponse;
use App\Modules\Core\XClient\Response\FlickrResponse;
use App\Modules\Core\XClient\Response\XClientResponseInterface;

class FlickrClientAdapter extends AbstractBaseClient
{
    public function request(
        string $method,
        string $url,
        array $payload = [],
        array $options = []
    ): XClientResponseInterface {

        $this->client->setHeaders($options['headers'] ?? []);
        if (str_contains($url, 'oauth')) {
            $response = new BaseResponse($this->client->request($url, $payload, $method));
        } else {
            $response = new FlickrResponse($this->client->request($url, $payload, $method));
        }

        return $response;
    }
}
