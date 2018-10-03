<?php


namespace Dymantic\SimpleWeather;


use Illuminate\Support\Carbon;

class SimpleWeather
{
    private $provider;

    public function __construct(WeatherProvider $provider)
    {
        $this->provider = $provider;
    }

    public function updateForecast($location)
    {
        $forecast = $this->provider->forecast($location);
        $cache_base = "weather.forecast.{$location->slug()}.";

        cache()->forever($cache_base . 'last_update', Carbon::now()->toIso8601String());
        cache()->forever($cache_base . 'days', $forecast);
    }

    public function storeCurrent($location)
    {
        WeatherRecord::create($this->provider->current($location));
    }
}