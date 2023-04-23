<?php

namespace App\Modules\Core\XClient\Response;

use Psr\Http\Message\ResponseInterface;

abstract class AbstractBaseResponse implements XClientResponseInterface
{
    protected bool $success = true;

    protected ?string $raw;

    /**
     * @var array|mixed
     */
    protected mixed $data;

    public function __construct(private ?ResponseInterface $response)
    {
        if (! $this->response
            || $this->response->getStatusCode() !== 200
        ) {
            $this->success = false;

            $this->raw = null;
        } else {
            $this->raw = $this->response->getBody()->getContents();
        }
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getRaw(): ?string
    {
        return $this->raw;
    }

    public function getProtocolVersion(): ?string
    {
        return $this->response?->getProtocolVersion();
    }

    public function getHeaders(): ?array
    {
        return $this->response?->getHeaders();
    }

    public function getStatusCode(): ?int
    {
        return $this->response?->getStatusCode();
    }

    public function getReasonPhrase(): string
    {
        return $this->response?->getReasonPhrase();
    }
}
