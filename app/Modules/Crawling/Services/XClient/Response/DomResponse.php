<?php

namespace App\Modules\Crawling\Services\XClient\Response;

use App\Modules\Core\Services\XClient\Response\XClientResponseInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class DomResponse implements XClientResponseInterface
{
    private bool $success = true;

    private ?string $raw;
    private Crawler $crawler;

    public function __construct(private ?ResponseInterface $response)
    {
        if (!$this->response
            || $this->response->getStatusCode() !== 200) {
            $this->success = false;

            $this->raw = null;
        } else {
            $this->raw = $this->response->getBody()->getContents();
        }

        $this->crawler = new Crawler($this->raw);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getRaw(): ?string
    {
        return $this->raw;
    }

    public function getData(): Crawler
    {
        return $this->crawler;
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
