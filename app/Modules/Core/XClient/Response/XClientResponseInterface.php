<?php

namespace App\Modules\Core\XClient\Response;

interface XClientResponseInterface
{
    public function isSuccess(): bool;

    public function getRaw(): ?string;

    public function getData(): mixed;

    public function getProtocolVersion(): ?string;

    public function getHeaders(): ?array;

    public function getStatusCode(): ?int;

    public function getReasonPhrase(): ?string;
}
