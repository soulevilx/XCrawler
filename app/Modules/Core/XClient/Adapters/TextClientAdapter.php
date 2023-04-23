<?php

namespace App\Modules\Core\XClient\Adapters;

use App\Modules\Core\XClient\Response\BaseResponse;
use App\Modules\Core\XClient\Response\XClientResponseInterface;
use GuzzleHttp\Exception\ClientException;

class TextClientAdapter extends AbstractBaseClient
{
    public function request(
        string $method,
        string $url,
        array $payload = [],
        array $options = []
    ): XClientResponseInterface {
        try {
            $response = new BaseResponse(
                $this->client->request($url, $payload, $method)
            );
        } catch (ClientException $e) {
            $response = new BaseResponse($e->getResponse());
        } finally {
            if (!isset($response)) {
                $response = new BaseResponse(null);
            }

            return $response;
        }
    }
}
