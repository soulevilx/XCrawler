<?php

namespace App\Modules\Core\Jobs\Middlewares;

use Illuminate\Support\Facades\Redis;

class LimitMiddleware
{
    public function __construct(
        private string $key,
        private int $block = 1,
        private int $allow = 1,
        private int $every = 1,
    ) {
    }

    /**
     * Process the queued job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     */
    public function handle($job, $next): void
    {
        Redis::throttle(
            md5(
                serialize(
                    [
                        config('app.key'),
                        $this->key,
                        config('app.server_id'),
                    ]
                )
            )
        )
            ->block($this->block)
            ->allow($this->allow)
            ->every($this->every)
            ->then(function () use ($job, $next) {
                $next($job);
            }, function () use ($job) {
                // Could not obtain lock...

                $job->release(10);
            });
    }
}
