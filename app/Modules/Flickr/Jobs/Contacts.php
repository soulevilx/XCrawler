<?php

namespace App\Modules\Flickr\Jobs;

use App\Modules\Flickr\Models\Contact;
use App\Modules\Flickr\Services\FlickrService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Contacts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $contacts = app(FlickrService::class)->contacts()->getList();
        /**
         * @TODO Move to service
         */
        foreach ($contacts as $contact) {
            Contact::updateOrCreate([
                'nsid' => $contact['nsid'],
            ], $contact);
        }
    }
}
