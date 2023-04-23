<?php

namespace App\Modules\Core\Tests\Unit\Services;

use App\Modules\Core\Services\SettingService;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    private SettingService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(SettingService::class);
    }

    public function testGet()
    {
        $this->assertNull(
            $this->service->get('test', 'time')
        );

        $this->assertTrue(
            $this->service->get('test', 'time', true)
        );
    }

    public function testSet()
    {
        $this->service->set('test', 'hello', 'world');
        $this->assertEquals('world', $this->service->get('test', 'hello'));
    }

    public function testRemember()
    {
        $this->service->set('test', 'hello', 'world');
        $this->assertEquals('world', $this->service->get('test', 'hello'));
        $this->assertEquals('world', $this->service->remember('test', 'hello', fn() => time()));
        $this->assertEquals('now', $this->service->remember('test', 'now', fn() => 'now'));
    }

    public function testForget()
    {
        $now = time();

        $this->service->remember('test', 'time', fn() => $now);
        $this->service->forget('test', 'time');

        $this->assertDatabaseMissing('settings', [
            'group' => 'test',
            'key' => 'time',
            'value' => $now,
        ], 'mongodb');
    }
}
