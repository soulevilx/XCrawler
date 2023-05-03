<?php

namespace App\Modules\Flickr\Console\Migrations;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigratePhotosets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:migrate-photosets';

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
        $photosetMongo = DB::connection('mongodb')->table('flickr_photosets');
        $count = $photosetMongo->count();
        $this->output->progressStart($count);
        $now = Carbon::now();

        $photosetMongo::cursor()->each(function ($photoset) use ($now) {
            DB::table('flickr_photosets')
                ->insertOrIgnore([
                    'uuid' => Str::orderedUuid(),
                    'id' => $photoset['id'],
                    'owner' => $photoset['owner'],
                    'primary' => $photoset['primary'],
                    'secret' => $photoset['secret'],
                    'server' => $photoset['server'],
                    'farm' => $photoset['farm'],
                    'title' => $photoset['title'],
                    'description' => $photoset['description'] ?? null,
                    'count_photos' => $photoset['count_photos'] ?? 0,
                    'count_videos' => $photoset['count_videos'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            $this->output->progressAdvance();
        });

        return 0;
    }
}

