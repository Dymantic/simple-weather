<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Providers\FakeWeatherProvider;
use Dymantic\SimpleWeather\WeatherProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class CurrentCommandTest extends TestCase
{
    /**
     *@test
     */
    public function the_current_weather_for_each_location_in_config_is_fetched()
    {
        $locations = [
            [
                'name' => 'location-one',
                'lat' => '33.333',
                'long' => '66.666',
                'identifier' => 'loc_1'
            ],
            [
                'name' => 'location-two',
                'lat' => '44.333',
                'long' => '88.666',
                'identifier' => 'loc_2'
            ]
        ];
        config()->set('simple-weather.locations', $locations);

        $provider = new FakeWeatherProvider();

        app()->instance(WeatherProvider::class, $provider);

        Artisan::call('simple-weather:current');

        $this->assertDatabaseHas('simple_weather', [
            'temp' => $provider->currentTemp,
            'condition' => $provider->currentCondition,
            'location_identifier' => 'loc_1'
        ]);

        $this->assertDatabaseHas('simple_weather', [
            'temp' => $provider->currentTemp,
            'condition' => $provider->currentCondition,
            'location_identifier' => 'loc_2'
        ]);
    }
}