<?php

namespace App\Modules\Core\XClient\Response;

use Symfony\Component\DomCrawler\Crawler;

class DomResponse extends AbstractBaseResponse
{
    public function getData(): Crawler
    {
        return new Crawler($this->raw);
    }
}
