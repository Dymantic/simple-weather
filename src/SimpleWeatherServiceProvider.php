<?php


namespace Dymantic\SimpleWeather;


use Dymantic\SimpleWeather\Providers\ApixuProvider;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class SimpleWeatherServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! class_exists('CreateSimpleWeatherTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_simple_weather_table.php.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_simple_weather_table.php'),
            ], 'migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/simple-weather.php' => config_path('simple-weather.php')
        ]);
    }

    public function register()
    {
        $this->app->bind(WeatherProvider::class, function() {
            $client = new Client();
            return new ApixuProvider(config('simple-weather.api_key'), $client);
        });

        $this->app->bind('weather', function() {
            $provider = $this->app->make(WeatherProvider::class);
            return new SimpleWeather($provider);
        });
    }
}