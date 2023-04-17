<?php

namespace App\Modules\Jav\Services\Crawlers;

use App\Modules\Crawling\Services\Crawlers\AbstractDomCrawlerAdapter;
use App\Modules\Jav\Events\OnejavItemParsed;
use App\Modules\Jav\Events\OnejavItemsRecursing;
use ArrayObject;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Symfony\Component\DomCrawler\Crawler;

class OnejavCrawlerAdapter extends AbstractDomCrawlerAdapter
{
    public const BASE_URL = 'https://onejav.com';
    public const DEFAULT_DATE_FORMAT = 'Y/m/d';

    public function items(string $url, array $data = []): Collection
    {
        $data = $data['page'] ?? 1;
        $data = $data['payload'] ?? [];

        $response = $this->service->request('GET', $url, $data);

        if (!$response->isSuccess()) {
            return collect();
        }

        return collect(
            $response->getData()->filter('.container .columns')
                ->each(function ($el) {
                    return $this->parse($el);
                })
        );
    }

    public function daily(): Collection
    {
        $items = collect();
        $this->itemsWithPageRecursive(
            $items,
            Carbon::now()->format(self::DEFAULT_DATE_FORMAT)
        );

        return $items;
    }

    public function search(string $keyword, string $by = 'search'): Collection
    {
        $items = collect();
        $this->itemsWithPageRecursive(
            $items,
            $by.'/'.urlencode($keyword)
        );

        return $items;
    }

    public function itemsWithPage(
        Collection &$items,
        string $url,
        array $payload = []
    ): int {

        $currentPage = !empty($payload['page']) ? $payload['page'] : 1;
        if (empty($payload['page'])) {
            $payload['page'] = $currentPage;
        }

        $response = $this->service->request(
            'GET',
            self::BASE_URL.'/'.$url,
            $payload
        );

        if (!$response->isSuccess()) {
            return 1;
        }

        $dom = $response->getData();
        $pageNode = $dom->filter('a.pagination-link')->last();

        $lastPage = 0 === $pageNode->count() ? 1 : (int) $pageNode->text();

        $items = $items->merge(
            collect($dom->filter('.container .columns')->each(function ($el) {
                return $this->parse($el);
            }))
        );

        return $lastPage;
    }

    public function itemsWithPageRecursive(Collection &$items, string $url, array $payload = []): int
    {
        $currentPage = !empty($payload['page']) ? $payload['page'] : 1;
        if (empty($payload['page'])) {
            $payload['page'] = $currentPage;
        }

        $response = $this->service->request(
            'GET',
            self::BASE_URL.'/'.$url,
            $payload
        );

        if (!$response->isSuccess()) {
            return 1;
        }

        $dom = $response->getData();
        $pageNode = $dom->filter('a.pagination-link')->last();

        $lastPage = 0 === $pageNode->count() ? 1 : (int) $pageNode->text();

        $items = $items->merge(
            collect($dom->filter('.container .columns')->each(function ($el) {
                return $this->parse($el);
            }))
        );

        if (empty($payload) || $payload['page'] < $lastPage) {
            Event::dispatch(new OnejavItemsRecursing());
            sleep(1);
            $lastPage = $this->itemsWithPageRecursive($items, $url, ['page' => $currentPage + 1]);
        }

        return $lastPage;
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
