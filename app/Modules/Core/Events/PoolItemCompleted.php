<?php

namespace App\Modules\Core\Events;

use App\Modules\Core\Models\Pool;
use Illuminate\Queue\SerializesModels;

class PoolItemCompleted
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Pool $pool)
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
