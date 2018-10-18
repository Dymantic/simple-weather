<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\FakeWeatherProvider;
use Dymantic\SimpleWeather\WeatherRecord;
use Illuminate\Support\Carbon;

class WeatherOverviewTest extends TestCase
{
    /**
     * @test
     */
    public function provides_an_overview_of_the_weather_for_a_location_for_a_given_date()
    {
        collect([0, 1, 2, 3])->each(function ($index) {
            WeatherRecord::create([
                'temp'                => (string)($index * 11.1),
                'condition'           => 'partly testy',
                'record_date'         => Carbon::today()->subDays($index),
                'location_identifier' => 'loc_1'
            ]);
        });

        cache()->set('weather.forecast.test-location.days', (new FakeWeatherProvider())->rawFakeArray(), 10);

        $expected = [
            'location' => ['name' => 'Test location'],
            'days'     => [
                [
                    'date'           => Carbon::today()->subDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 33,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 22,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 11,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->format('Y-m-d'),
                    'day_name'       => Carbon::today()->format('l'),
                    'day_name_short' => Carbon::today()->format('D'),
                    'is_today'       => true,
                    'temp'           => 0,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->addDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ]
            ]
        ];
        $location = new Location('Test location', '24', '121', 'loc_1');
        $this->assertEquals($expected, app('weather')->overview($location));
    }

    /**
     * @test
     */
    public function it_uses_forecasted_day_if_today_has_not_been_recorded()
    {
        collect([1, 2, 3])->each(function ($index) {
            WeatherRecord::create([
                'temp'                => (string)($index * 11.1),
                'condition'           => 'partly testy',
                'record_date'         => Carbon::today()->subDays($index),
                'location_identifier' => 'loc_1'
            ]);
        });

        $today = [
            [
                'date'      => Carbon::today()->format('Y-m-d'),
                'min'       => 20,
                'max'       => 50,
                'condition' => 'Sunny'
            ]
        ];
        $forecast = array_merge($today, (new FakeWeatherProvider())->rawFakeArray());

        cache()->set('weather.forecast.test-location.days', $forecast, 10);

        $expected = [
            'location' => ['name' => 'Test location'],
            'days'     => [
                [
                    'date'           => Carbon::today()->subDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 33,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 22,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 11,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->format('Y-m-d'),
                    'day_name'       => Carbon::today()->format('l'),
                    'day_name_short' => Carbon::today()->format('D'),
                    'is_today'       => true,
                    'temp'           => 50,
                    'condition'      => 'Sunny'
                ],
                [
                    'date'           => Carbon::today()->addDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ]
            ]
        ];
        $location = new Location('Test location', '24', '121', 'loc_1');
        $this->assertEquals($expected, app('weather')->overview($location));
    }

    /**
     * @test
     */
    public function the_overview_is_empty_if_there_is_no_current_day()
    {
        $location = new Location('Test location', '24', '121', 'loc_1');
        $overview = app('weather')->overview($location);

        $this->assertEquals([
            'location' => ['name' => $location->name],
            'days'     => []
        ], $overview);
    }

    /**
     * @test
     */
    public function the_overview_does_not_contain_past_days_that_may_be_in_the_cache()
    {
        collect([1, 2, 3])->each(function ($index) {
            WeatherRecord::create([
                'temp'                => (string)($index * 11.1),
                'condition'           => 'partly testy',
                'record_date'         => Carbon::today()->subDays($index),
                'location_identifier' => 'loc_1'
            ]);
        });

        $today = [
            [
                'date'      => Carbon::today()->subDay()->format('Y-m-d'),
                'min'       => 1,
                'max'       => 99,
                'condition' => 'Exceptional'
            ],
            [
                'date'      => Carbon::today()->format('Y-m-d'),
                'min'       => 20,
                'max'       => 50,
                'condition' => 'Sunny'
            ]
        ];
        $forecast = array_merge($today, (new FakeWeatherProvider())->rawFakeArray());

        cache()->set('weather.forecast.test-location.days', $forecast, 10);

        $expected = [
            'location' => ['name' => 'Test location'],
            'days'     => [
                [
                    'date'           => Carbon::today()->subDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 33,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 22,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->subDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->subDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->subDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 11,
                    'condition'      => 'partly testy'
                ],
                [
                    'date'           => Carbon::today()->format('Y-m-d'),
                    'day_name'       => Carbon::today()->format('l'),
                    'day_name_short' => Carbon::today()->format('D'),
                    'is_today'       => true,
                    'temp'           => 50,
                    'condition'      => 'Sunny'
                ],
                [
                    'date'           => Carbon::today()->addDays(1)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(1)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(1)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(2)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(2)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(2)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ],
                [
                    'date'           => Carbon::today()->addDays(3)->format('Y-m-d'),
                    'day_name'       => Carbon::today()->addDays(3)->format('l'),
                    'day_name_short' => Carbon::today()->addDays(3)->format('D'),
                    'is_today'       => false,
                    'temp'           => 30,
                    'condition'      => 'Partly cloudy'
                ]
            ]
        ];
        $location = new Location('Test location', '24', '121', 'loc_1');
        $this->assertEquals($expected, app('weather')->overview($location));
    }

}