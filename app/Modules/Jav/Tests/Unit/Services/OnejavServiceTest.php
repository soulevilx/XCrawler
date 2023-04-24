<?php

namespace App\Modules\Jav\Tests\Unit\Services;

use App\Modules\Core\Facades\Setting;
use App\Modules\Core\XClient\XClient;
use App\Modules\Jav\Crawlers\OnejavCrawler;
use App\Modules\Jav\Events\Onejav\OnejavAllCompleted;
use App\Modules\Jav\Events\Onejav\OnejavAllProcessing;
use App\Modules\Jav\Services\OnejavService;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class OnejavServiceTest extends TestCase
{
    public function testDaily()
    {
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                for ($index = 1; $index <= 6; $index++) {
                    $response = new Response(
                        200,
                        [],
                        file_get_contents(__DIR__.'/../../Fixtures/Onejav/2023_04_13_'.$index.'.html'),
                    );

                    $mock->shouldReceive('request')
                        ->with(
                            OnejavCrawler::BASE_URL.'/'.Carbon::now()->format(OnejavCrawler::DEFAULT_DATE_FORMAT),
                            [
                                'page' => $index,
                            ],
                            'GET'
                        )->andReturn($response);
                }
            })
        );

        app(OnejavService::class)->daily();
        $this->assertDatabaseCount('onejav', 53);
    }

    public function testAll()
    {
        Event::fake([
            OnejavAllProcessing::class,
            OnejavAllCompleted::class
        ]);
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                for ($index = 1; $index <= 6; $index++) {
                    $response = new Response(
                        200,
                        [],
                        file_get_contents(__DIR__.'/../../Fixtures/Onejav/2023_04_13_'.$index.'.html'),
                    );

                    $mock->shouldReceive('request')
                        ->with(

                            OnejavCrawler::BASE_URL.'/new',
                            [
                                'page' => $index,
                            ],
                            'GET'
                        )->andReturn($response);
                }
            })
        );

        Setting::forget('onejav', 'pages');
        Setting::forget('onejav', 'current_page');

        $service = app(OnejavService::class);
        $service->all(); // 1
        $this->assertEquals(4, Setting::get('onejav', 'pages'));
        $this->assertEquals(2, Setting::get('onejav', 'current_page'));
        $service->all(); // 2
        $this->assertEquals(5, Setting::get('onejav', 'pages'));
        $this->assertEquals(3, Setting::get('onejav', 'current_page'));
        $service->all(); // 3
        $this->assertEquals(6, Setting::get('onejav', 'pages'));
        $this->assertEquals(4, Setting::get('onejav', 'current_page'));
        $service->all(); // 4
        $this->assertEquals(6, Setting::get('onejav', 'pages'));
        $this->assertEquals(5, Setting::get('onejav', 'current_page'));
        $service->all(); // 5
        $this->assertEquals(6, Setting::get('onejav', 'pages'));
        $this->assertEquals(6, Setting::get('onejav', 'current_page'));
        $service->all(); // 6
        $this->assertEquals(6, Setting::get('onejav', 'pages'));
        $this->assertEquals(1, Setting::get('onejav', 'current_page'));

        Event::assertDispatchedTimes(OnejavAllProcessing::class, 6);
        Event::assertDispatched(OnejavAllCompleted::class);
    }
}
