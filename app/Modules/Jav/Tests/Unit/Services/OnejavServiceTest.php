<?php

namespace App\Modules\Jav\Tests\Unit\Services;

use App\Modules\Core\Facades\Setting;
use App\Modules\Core\Services\XClient\XClient;
use App\Modules\Jav\Events\OnejavAllProcessing;
use App\Modules\Jav\Events\OnejavItemsRecursing;
use App\Modules\Jav\Services\Crawlers\OnejavCrawlerAdapter;
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
        Event::fake([OnejavItemsRecursing::class]);

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

                            OnejavCrawlerAdapter::BASE_URL.'/'.Carbon::now()->format(OnejavCrawlerAdapter::DEFAULT_DATE_FORMAT),
                            [
                                'page' => $index
                            ],
                            'GET'
                        )->andReturn($response);
                }
            })
        );

        $service = app(OnejavService::class);
        $items = $service->daily();

        $this->assertCount(53, $items);
        $this->assertDatabaseCount('onejav', $items->count());
        Event::assertDispatched(OnejavItemsRecursing::class, 5);
    }

    public function testAll()
    {
        Event::fake([OnejavAllProcessing::class]);
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

                            OnejavCrawlerAdapter::BASE_URL.'/new',
                            [
                                'page' => $index
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
        $service->all(); // 2
        $service->all(); // 3
        $service->all(); // 4
        $service->all(); // 5

        $this->assertEquals(6, Setting::get('onejav', 'pages'));
        $this->assertEquals(6, Setting::get('onejav', 'current_page'));

        Event::assertDispatchedTimes(OnejavAllProcessing::class, 5);
    }

}
