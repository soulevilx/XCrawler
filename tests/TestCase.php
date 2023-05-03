<?php

namespace Tests;

use App\Modules\Core\Models\Pool;
use App\Modules\Core\Models\RequestLog;
use App\Modules\Core\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use WithFaker;
    use DatabaseMigrations;
    use RefreshDatabase {
        RefreshDatabase::refreshDatabase as refreshSchema;
    }

    public function refreshDatabase()
    {
        if (!App::environment('testing')) {
            return;
        }

        Setting::truncate();
        RequestLog::truncate();
        Pool::truncate();

        $this->artisan('db:wipe');
        $this->artisan('migrate:fresh');

        Cache::store('redis')->flush();
    }
}
