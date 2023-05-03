<?php

namespace App\Modules\Flickr\Console\Migrations;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MigratePhotos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'flickr:migrate-photos';

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
        $photoMongo = DB::connection('mongodb')->table('flickr_photos');
        $count = $photoMongo::count();
        $this->output->progressStart($count);
        $now = Carbon::now();

        $photoMongo::cursor()->each(function ($photo) use ($now) {
            DB::table('flickr_photos')
                ->insertOrIgnore([
                    'uuid' => Str::orderedUuid(),
                    'id' => $photo->id,
                    'owner' => $photo->owner,
                    'secret' => $photo->secret,
                    'server' => $photo->server,
                    'farm' => $photo->farm,
                    'title' => $photo->title,
                    'ispublic' => $photo->ispublic,
                    'isfriend' => $photo->isfriend,
                    'isfamily' => $photo->isfamily,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            $this->output->progressAdvance();
        });
    }
}

