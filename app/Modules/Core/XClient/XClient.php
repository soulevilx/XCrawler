<?php

namespace App\Modules\Core\XClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RedirectMiddleware;
use Illuminate\Log\Logger;
use Psr\Http\Message\ResponseInterface;

class XClient
{
    protected Client $client;

    protected Response $response;

    protected array $headers;

    protected string $contentType = 'application/x-www-form-urlencoded';

    public function __construct(
        protected array $settings = [],
        protected array $requestOptions = []
    ) {
        $this->settings = [
            'maxRetries' => 3, 'delayInSec' => 1, 'minErrorCode' => 500, 'logger' => [
                'instance' => app(Logger::class),
                'formatter' => null,
            ], 'caching' => [
                'instance' => null,
            ], ...$settings,
        ];
        $this->requestOptions = [
            'allow_redirects' => RedirectMiddleware::$defaultSettings, 'http_errors' => true, 'decode_content' => true,
            'verify' => true, 'cookies' => false, 'idn_conversion' => false, ...$requestOptions,
        ];
    }

    /**
     * Set the headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = array_merge($this->headers ?? [], $headers);

        return $this;
    }

    /**
     * Set Client options
     *
     * @return $this
     */
    public function setRequestOptions(array $requestOptions): self
    {
        $this->requestOptions = array_merge($this->requestOptions, $requestOptions);

        return $this;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }

    /**
     * @param  string  $contentType
     * @return $this
     */
    public function setContentType(string $contentType = 'json'): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function init(array $settings = [], array $requestOptions = []): self
    {
        $this->setSettings($settings);
        $this->setRequestOptions($requestOptions);

        $factory = app()->makeWith(
            Factory::class,
            ['fakeResponseCode' => $this->settings['fakeResponseCode'] ?? null]
        )
            ->enableRetries($this->settings['maxRetries'], $this->settings['delayInSec'], $this->settings['minErrorCode'])
            ->addOptions($this->requestOptions);

        if ($this->settings['logger']['instance']) {
            $factory->enableLogging(
                $this->settings['logger']['instance'],
                $this->settings['logger']['formatter'] ?? MessageFormatter::SHORT
            );
        }

        if ($this->settings['caching']['instance']) {
            $factory->enableCache($this->settings['caching']['instance']);
        }

        /**
         * Client inited w/ options
         */
        $this->client = $factory->make();

        return $this;
    }

    /**
     * Perform the request
     *
     * @throws GuzzleException
     */
    public function request(
        string $endpoint,
        array $payload = [],
        string $method = 'GET'
    ): ResponseInterface {
        /**
         * Request options
         */
        $requestOptions = array_merge($this->requestOptions, ['headers' => $this->headers ?? []]);

        $payload = $this->convertToUTF8($payload);

        if ($method == 'GET') {
            $requestOptions['query'] = $payload;
        } else {
            switch ($this->contentType) {
                case 'application/x-www-form-urlencoded':
                    $requestOptions['form_params'] = $payload;
                    break;
                default:
                case 'json':
                    $requestOptions['json'] = $payload;
                    break;
            }
        }

        return $this->client->request($method, $endpoint, $requestOptions);
    }

    public function get(string $endpoint, array $payload = []): Response
    {
        return $this->request($endpoint, $payload);
    }

    /**
     * POST Request
     */
    public function post(string $endpoint, array $payload = []): Response
    {
        return $this->request($endpoint, $payload, 'POST');
    }

    /**
     * PUT Request
     */
    public function put(string $endpoint, array $payload = []): Response
    {
        return $this->request($endpoint, $payload, 'PUT');
    }

    /**
     * PATCH Request
     */
    public function patch(string $endpoint, array $payload = []): Response
    {
        return $this->request($endpoint, $payload, 'PATCH');
    }

    /**
     * DELETE Request
     */
    public function delete(string $endpoint, array $payload = []): Response
    {
        return $this->request($endpoint, $payload, 'DELETE');
    }

    /**
     * Sanitize payload to UTF-8
     */
    protected function convertToUTF8(array $array): array
    {
        array_walk_recursive($array, function (&$item) {
            if (!mb_detect_encoding($item, 'utf-8', true)) {
                $item = utf8_encode($item);
            }
        });

        return $array;
    }

    /**
     * Get the Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
