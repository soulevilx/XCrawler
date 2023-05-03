<?php

namespace App\Modules\Flickr\Console\Migrations;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigrateContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:migrate-contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $contactMongo = DB::connection('mongodb')->table('flickr_contacts');
        $count = $contactMongo->count();

        $this->output->progressStart($count);
        $now = Carbon::now();
        $contactMongo->cursor()->each(function ($contact) use ($now) {
            DB::table('flickr_contacts')
                ->insertOrIgnore([
                    'uuid' => Str::orderedUuid(),
                    'nsid' => $contact['nsid'],
                    'username' => $contact['username'],
                    'iconserver' => $contact['iconserver'],
                    'iconfarm' => $contact['iconfarm'],
                    'ignored' => $contact['ignored'],
                    'rev_ignored' => $contact['rev_ignored'] ?? null,
                    'realname' => $contact['realname'] ?? null,
                    'friend' => $contact['friend'],
                    'family' => $contact['family'],
                    'path_alias' => $contact['path_alias'],
                    'location' => $contact['location'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            $this->output->progressAdvance();
        });
    }
}

