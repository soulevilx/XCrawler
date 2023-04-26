<?php

namespace App\Modules\Flickr\Observer;

use App\Modules\Core\Facades\Pool as PoolFacade;
use App\Modules\Core\Models\Pool;
use App\Modules\Core\Services\Pool\PoolService;
use App\Modules\Flickr\Jobs\Queues\Photos;
use App\Modules\Flickr\Models\Contact;

class ContactObserver
{
    public function created(Contact $model)
    {
        // Get user's photos
        if (!Pool::where('nsid', $model->nsid)->where('job', Photos::class)->exists()) {
            PoolFacade::add(
                Photos::class,
                [
                    'nsid' => $model->nsid,
                ],
                PoolService::QUEUE_API
            );
        }
    }

    public function updated(Contact $model)
    {
    }
}
