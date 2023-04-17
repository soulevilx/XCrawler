<?php

namespace App\Modules\Jav\Services;

use App\Modules\Core\Facades\Setting;
use App\Modules\Jav\Events\OnejavAllProcessing;
use App\Modules\Jav\Repositories\OnejavRepository;
use App\Modules\Jav\Services\Crawlers\OnejavCrawlerAdapter;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class OnejavService
{
    private JavClient $client;

    public function __construct()
    {
        $this->client = app()
            ->makeWith(
                JavClient::class,
                [
                    'crawler' => app(OnejavCrawlerAdapter::class)
                ]
            );
    }

    public function daily(): Collection
    {
        return $this->client->crawlWithRecursive(
            'itemsWithPageRecursive',
            Carbon::now()->format(OnejavCrawlerAdapter::DEFAULT_DATE_FORMAT)
        );
    }

    public function all()
    {
        $currentPage = Setting::remember('onejav', 'current_page', fn() => 1);

        Event::dispatch(new OnejavAllProcessing($currentPage));

        $this->client->crawlWithRecursive(
            'itemsWithPage',
            'new',
            ['page' => $currentPage + 1],
            $lastPage
        );

        if ($currentPage === $lastPage || $currentPage > $lastPage) {
            $currentPage = 0;
        }


        Setting::forget('onejav', 'pages');
        Setting::remember('onejav', 'pages', fn() => $lastPage);

        Setting::forget('onejav', 'current_page');
        Setting::remember('onejav', 'current_page', fn() => $currentPage + 1);
    }

    public function create(array $attributes)
    {
        app(OnejavRepository::class)->create($attributes);
    }
}
