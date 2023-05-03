<?php

namespace App\Modules\Core\XClient;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Factory
{
    private HandlerStack $handler;

    private array $options;

    private Client $client;

    private array $history = [];

    public function __construct(public ?int $fakeResponseCode = null)
    {
        $this->reset();
    }

    public function addOptions(array $options): self
    {
        $this->options = [...$this->options ?? [], ...$options];

        return $this;
    }

    public function enableLogging(
        LoggerInterface $logger,
        string $format = MessageFormatter::SHORT,
        string $level = LogLevel::INFO
    ): self {

        return $this->withMiddleware(
            Middleware::log($logger, new MessageFormatter($format), $level),
            'log'
        );
    }

    public function enableRetries(int $maxRetries = 3, int $delayInSec = 1, int $minErrorCode = 500): self
    {
        $decider = static function ($retries, $_, $response) use ($maxRetries, $minErrorCode) {
            return $retries < $maxRetries
                && $response instanceof ResponseInterface
                && $response->getStatusCode() >= $minErrorCode;
        };

        $increasingDelay = static fn ($attempt) => $attempt * $delayInSec * 1000;

        return $this->withMiddleware(
            Middleware::retry($decider, $increasingDelay),
            'retry'
        );
    }

    public function enableCache($cache): self
    {
        $this->withMiddleware($cache, 'cache');

        return $this;
    }

    public function make(): Client
    {
        $this->client = app()->makeWith(
            Client::class,
            [
                'config' => ['handler' => $this->handler] + ($this->options ?? []),
            ]
        );

        if ($this->fakeResponseCode) {
            /**
             * @link https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
             */
            $this->history[$id = spl_object_id($this->client)] = [];
            $this->withMiddleware(
                Middleware::history($this->history[$id]),
                'fake_history'
            );
        }

        $this->reset();

        return $this->client;
    }

    public function withMiddleware(callable $middleware, string $name = ''): self
    {
        $this->handler->push($middleware, $name);

        return $this;
    }

    public function getHistory($client): array
    {
        return $this->history[spl_object_id($client)] ?? [];
    }

    public function reset(): self
    {
        if ($this->fakeResponseCode) {
            $mockHandler = new MockHandler;
            $responseCallback = function (RequestInterface $request) use ($mockHandler, &$responseCallback) {
                $mockHandler->append($responseCallback);

                return new Response($this->fakeResponseCode, ['Content-Type' => 'text/plain'], sprintf(
                    'Fake test response for request: %s %s',
                    $request->getMethod(),
                    $request->getUri(),
                ));
            };
            $mockHandler->append($responseCallback);
        }

        /**
         * Init default
         */
        $this->handler = HandlerStack::create($mockHandler ?? null);

        return $this;
    }
}
