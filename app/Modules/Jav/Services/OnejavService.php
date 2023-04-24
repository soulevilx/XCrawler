<?php

namespace App\Modules\Jav\Services;

use App\Modules\Core\Facades\Setting;
use App\Modules\Jav\Crawlers\OnejavCrawler;
use App\Modules\Jav\Events\Onejav\OnejavAllCompleted;
use App\Modules\Jav\Events\Onejav\OnejavAllProcessing;
use App\Modules\Jav\Jobs\Onejav\FetchItems;
use App\Modules\Jav\Repositories\OnejavRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class OnejavService
{
    public function __construct(private OnejavCrawler $crawler)
    {
    }

    public function daily()
    {
        FetchItems::dispatch(Carbon::now()->format(OnejavCrawler::DEFAULT_DATE_FORMAT));
    }

    public function all()
    {
        $currentPage = Setting::remember('onejav', 'current_page', fn () => 1);

        Event::dispatch(new OnejavAllProcessing($currentPage));

        $this->crawler->items('new', ['page' => $currentPage ]);
        $lastPage = $this->crawler->lastPage();

        if ($currentPage >= $lastPage) {
            $currentPage = 0;
            Event::dispatch(new OnejavAllCompleted);
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
