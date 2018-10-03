<?php


namespace Dymantic\SimpleWeather;


interface WeatherProvider
{
    public function forecast(Location $location);

    public function current(Location $location);
}