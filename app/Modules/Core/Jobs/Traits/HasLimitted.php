<?php

namespace App\Modules\Core\Jobs\Traits;

use App\Modules\Core\Facades\Setting;
use App\Modules\Core\Jobs\Middlewares\LimitMiddleware;

trait HasLimitted
{
    /**
     * The number of times the queued listener may be attempted.
     *
     * @var int
     */
    public int $tries = 5;

    public function middleware()
    {
        if (config('app.env') === 'testing') {
            return [];
        }

        return [
            new LimitMiddleware(
                self::class,
                $this->block ?? 1,
                $this->allow ?? 1,
                $this->every ?? 1,
            ),
        ];
    }

    public function retryUntil()
    {
        return now()->addMinutes(Setting::remember('core', 'job_retryUntil', fn () => 60));
    }
}
