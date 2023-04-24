<?php

namespace App\Modules\Flickr\Jobs;

use App\Modules\Core\Jobs\AbstractApiQueue;
use App\Modules\Core\OAuth\OAuth1\Providers\Flickr;
use App\Modules\Core\OAuth\ProviderFactory;
use App\Modules\Flickr\Events\FetchedFlickrItems;
use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Support\Facades\Event;

/**
 * Execute fetch items with getList method
 */
class GetList extends AbstractApiQueue
{
    public FlickrService $service;

    public function __construct(
        public string $method,
        public string $listEntities,
        public string $listEntity,
        public array $params = [],
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = app(ProviderFactory::class)->make(app(Flickr::class))->request(
            $this->method,
            $this->params
        )->getData();

        if (!isset($data[$this->listEntities])) {
            return;
        }

        Event::dispatch(new FetchedFlickrItems($data, $this->listEntities, $this->listEntity));

        $page = (int) $data[$this->listEntities]['page'];
        $pages = (int) $data[$this->listEntities]['pages'];

        if ($page !== 1) {
            return;
        }

        for ($nextPage = $page + 1; $nextPage <= $pages; $nextPage++) {
            self::dispatch(
                $this->method,
                $this->listEntities,
                $this->listEntity,
                [...$this->params, 'page' => $nextPage]
            );
        }
    }
}
