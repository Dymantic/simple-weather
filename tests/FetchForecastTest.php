<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\FakeWeatherProvider;
use Dymantic\SimpleWeather\WeatherProvider;
use Dymantic\SimpleWeather\WeatherRecord;
use Illuminate\Support\Carbon;

class FetchForecastTest extends TestCase
{
    /**
     * @test
     */
    public function the_weather_service_fetches_the_forecast_and_caches_results()
    {
        $fakeProvider = new FakeWeatherProvider();
        app()->bind(WeatherProvider::class, function() use ($fakeProvider) {
            return $fakeProvider;
        });

        app('weather')->updateForecast(new Location('test', '24.333', '121.7999', 'test_id'));

        $this->assertTrue(cache()->has('weather.forecast.test.last_update'));
        $this->assertTrue(cache()->has('weather.forecast.test.days'));

        $this->assertTrue(Carbon::parse(cache()->get('weather.forecast.test.last_update'))->isToday());
        $this->assertEquals($fakeProvider->rawFakeArray(), cache()->get('weather.forecast.test.days'));

    }

    /**
     *@test
     */
    public function the_weather_service_can_get_the_current_weather_and_store_in_db()
    {
        $fakeProvider = new FakeWeatherProvider();
        app()->bind(WeatherProvider::class, function() use ($fakeProvider) {
            return $fakeProvider;
        });

        app('weather')->storeCurrent(new Location('test', '24.333', '121.7999', 'test_id'));

        $this->assertCount(1, WeatherRecord::all());
        $record = WeatherRecord::first();

        $this->assertEquals($record->temp, $fakeProvider->currentTemp);
        $this->assertEquals($record->condition, $fakeProvider->currentCondition);
        $this->assertTrue($record->record_date->isToday());

    }

    /**
     *@test
     */
    public function weather_records_will_overwrite_any_existing_records_for_the_same_day_and_location()
    {
        $fakeProvider = new FakeWeatherProvider();
        app()->bind(WeatherProvider::class, function() use ($fakeProvider) {
            return $fakeProvider;
        });

        app('weather')->storeCurrent(new Location('test', '24.333', '121.7999', 'test_id'));
        app('weather')->storeCurrent(new Location('test', '24.333', '121.7999', 'test_id'));

        $this->assertCount(1, WeatherRecord::all());
        $record = WeatherRecord::first();

        $this->assertEquals($record->temp, $fakeProvider->currentTemp);
        $this->assertEquals($record->condition, $fakeProvider->currentCondition);
        $this->assertTrue($record->record_date->isToday());
    }
}