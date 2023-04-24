<?php

namespace App\Modules\Jav\Crawlers;

use App\Modules\Core\Services\CrawlerFactory;
use App\Modules\Core\Services\CrawlingService;
use App\Modules\Core\XClient\Adapters\DomClientAdapter;
use App\Modules\Jav\Events\Onejav\OnejavItemParsed;
use ArrayObject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Symfony\Component\DomCrawler\Crawler;

class OnejavCrawler
{
    public const BASE_URL = 'https://onejav.com';

    public const DEFAULT_DATE_FORMAT = 'Y/m/d';
    private CrawlingService $service;

    private int $lastPage = 1;

    public function __construct()
    {
        $this->service = app(CrawlerFactory::class)
            ->make(app(DomClientAdapter::class));
    }

    public function lastPage(): int
    {
        return $this->lastPage;
    }

    public function items(string $url, array $payload = []): Collection
    {
        $payload['page'] = $payload['page'] ?? 1;

        $response = $this->service->request('GET', self::BASE_URL.'/'.$url, $payload);

        if (!$response->isSuccess()) {
            return collect();
        }

        $dom = $response->getData();
        $pageNode = $dom->filter('a.pagination-link')->last();
        $this->lastPage = $pageNode->count() === 0 ? 1 : (int) $pageNode->text();

        return collect(
            $response->getData()->filter('.container .columns')
                ->each(function ($el) {
                    return $this->parse($el);
                })
        );
    }

    private function parse(Crawler $crawler): ?ArrayObject
    {
        $item = new ArrayObject([], ArrayObject::ARRAY_AS_PROPS);

        if ($crawler->filter('h5.title a')->count()) {
            $item->url = trim($crawler->filter('h5.title a')->attr('href'));
        }

        if ($crawler->filter('.columns img.image')->count()) {
            $item->cover = trim($crawler->filter('.columns img.image')->attr('src'));
        }

        if ($crawler->filter('h5 a')->count()) {
            $item->dvd_id = (trim($crawler->filter('h5 a')->text(null, false)));
            $item->dvd_id = implode(
                '-',
                preg_split('/(,?\\s+)|((?<=[a-z])(?=\\d))|((?<=\\d)(?=[a-z]))/i', $item->dvd_id)
            );
        }

        if ($crawler->filter('h5 span')->count()) {
            $item->size = trim($crawler->filter('h5 span')->text(null, false));

            if (str_contains($item->size, 'MB')) {
                $item->size = (float) trim(str_replace('MB', '', $item->size));
                $item->size /= 1024;
            } elseif (str_contains($item->size, 'GB')) {
                $item->size = (float) trim(str_replace('GB', '', $item->size));
            }
        }

        // Always use href because it'll never change but text will be
        $item->date = $this->convertStringToDateTime(trim($crawler->filter('.subtitle.is-6 a')->attr('href')));
        $item->genres = collect($crawler->filter('.tags .tag')->each(
            function ($genres) {
                return trim($genres->text(null, false));
            }
        ))->reject(function ($value) {
            return empty($value);
        })->unique()->toArray();

        // Description
        $description = $crawler->filter('.level.has-text-grey-dark');
        $item->description = $description->count() ? trim($description->text(null, false)) : null;
        $item->description = preg_replace("/\r|\n/", '', $item->description);

        $item->performers = collect($crawler->filter('.panel .panel-block')->each(
            function ($performers) {
                return trim($performers->text(null, false));
            }
        ))->reject(function ($value) {
            return empty($value);
        })->unique()->toArray();

        $item->torrent = trim($crawler->filter('.control.is-expanded a')->attr('href'));

        // Gallery. Only for FC
        $gallery = $crawler->filter('.columns .column a img');
        if ($gallery->count()) {
            $item->gallery = collect($gallery->each(
                function ($image) {
                    return trim($image->attr('src'));
                }
            ))->reject(function ($value) {
                return empty($value);
            })->unique()->toArray();
        }

        Event::dispatch(new OnejavItemParsed($item));

        return $item;
    }

    private function convertStringToDateTime(string $date): ?Carbon
    {
        if (!$dateTime = Carbon::createFromFormat(self::DEFAULT_DATE_FORMAT, trim($date, '/'))) {
            return null;
        }

        return $dateTime;
    }
}
