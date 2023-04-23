<?php

namespace App\Modules\Jav\Tests\Unit\Crawlers;

use App\Modules\Core\XClient\XClient;
use App\Modules\Jav\Crawlers\OnejavCrawler;
use App\Modules\Jav\Events\OnejavItemParsed;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Event;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class OnejavCrawlerTest extends TestCase
{
    public function testCrawlItems()
    {
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $response = new Response(
                    200,
                    [],
                    file_get_contents(__DIR__.'/../../Fixtures/Onejav/iori_tsukimi_1.html'),
                    '1.1',
                    'OK'
                );

                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                $mock->shouldReceive('request')->andReturn($response);
            })
        );

        $client = app(OnejavCrawler::class);

        $items = $client->items(
            $this->faker->url,
            [
                'page' => 1,
                'payload' => [],
            ]
        );

        $this->assertCount(10, $items);

        $dvdIds = [
            'MXGS1286',
            'MADM165',
            'BONY037',
            'NACR628',
            'GVH488',
            'NACR610',
            'MXGS1268',
            'GVH481',
            'PPPE088',
            'PPPE088',
        ];
        $urls = [
            'mxgs1286',
            'madm165',
            'bony037',
            'nacr628',
            'gvh488',
            'nacr610',
            'mxgs1268',
            'gvh481',
            'pppe088',
            'pppe088_2',
        ];

        $this->assertTrue(
            $items->contains(function ($item) use ($dvdIds, $urls) {
                $dvdId = str_replace('-', '', $item->dvd_id);
                $url = str_replace('/torrent/', '', $item->url);
                $result =
                    in_array(
                        $dvdId,
                        $dvdIds
                    )
                    && in_array('Iori Tsukimi', $item->performers)
                    && in_array($url, $urls);

                $index = array_search($dvdId, $dvdIds);
                if ($index) {
                    unset($dvdIds[$index]);
                }

                $index = array_search($url, $urls);
                if ($index) {
                    unset($urls[$index]);
                }

                return $result;
            })
        );

        $this->assertDatabaseCount('onejav', $items->count());
        foreach ($items as $item) {
            foreach ($item['performers'] as $performer) {
                $this->assertDatabaseHas('performers', [
                    'name' => $performer,
                ]);
            }

            foreach ($item['genres'] as $genre) {
                $this->assertDatabaseHas('genres', [
                    'name' => $genre,
                ]);
            }
        }
    }

    public function testCrawItemsRecursive()
    {
        Event::fake([OnejavItemParsed::class]);
        $this->instance(
            XClient::class,
            Mockery::mock(XClient::class, function (MockInterface $mock) {
                $mock->shouldReceive('init');
                $mock->shouldReceive('setHeaders');
                for ($index = 1; $index <= 2; $index++) {
                    $response = new Response(
                        200,
                        [],
                        file_get_contents(__DIR__.'/../../Fixtures/Onejav/iori_tsukimi_'.$index.'.html'),
                    );

                    $mock->shouldReceive('request')
                        ->with(
                            OnejavCrawler::BASE_URL.'/actress/Iori Tsukimi',
                            [
                                'page' => $index,
                            ],
                            'GET'
                        )->andReturn($response);
                }
            })
        );

        $client = app(OnejavCrawler::class);
        $items = collect();
        $client->itemsWithPageRecursive(
            $items,
            'actress/Iori Tsukimi',
        );

        $this->assertCount(15, $items);
        Event::assertDispatchedTimes(OnejavItemParsed::class, 15);
    }
}
