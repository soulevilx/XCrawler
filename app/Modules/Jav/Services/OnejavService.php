<?php

namespace App\Modules\Jav\Services;

use App\Modules\Core\Facades\Setting;
use App\Modules\Jav\Crawlers\OnejavCrawler;
use App\Modules\Jav\Events\OnejavAllProcessing;
use App\Modules\Jav\Repositories\OnejavRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

class OnejavService
{
    public function __construct(private OnejavCrawler $crawler)
    {
    }

    public function daily(): Collection
    {
        return $this->crawler->daily();
    }

    public function all()
    {
        $currentPage = Setting::remember('onejav', 'current_page', fn () => 0);

        Event::dispatch(new OnejavAllProcessing($currentPage));

        $items = collect();
        $lastPage = $this->crawler->itemsWithPage(
            $items,
            'new',
            ['page' => $currentPage + 1],
        );

        if ($currentPage === $lastPage || $currentPage > $lastPage) {
            $currentPage = 0;
        }


        Setting::forget('onejav', 'pages');
        Setting::remember('onejav', 'pages', fn () => $lastPage);

        Setting::forget('onejav', 'current_page');
        Setting::remember('onejav', 'current_page', fn () => $currentPage + 1);
    }

    public function create(array $attributes)
    {
        app(OnejavRepository::class)->create($attributes);
    }
}
