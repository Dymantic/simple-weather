<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Providers\FakeWeatherProvider;
use Dymantic\SimpleWeather\WeatherProvider;
use Illuminate\Support\Facades\Artisan;

class ForecastCommandTest extends TestCase
{
    /**
     *@test
     */
    public function the_forecast_for_locations_in_cofig_are_fetched()
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

        Artisan::call('simple-weather:forecast');

        $this->assertEquals($provider->rawFakeArray(), cache('weather.forecast.location-one.days'));
        $this->assertEquals($provider->rawFakeArray(), cache('weather.forecast.location-two.days'));


    }
}