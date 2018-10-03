<?php

namespace Dymantic\SimpleWeather\Commands;

use Dymantic\SimpleWeather\Location;
use Illuminate\Console\Command;

class UpdateForecasts extends Command
{
    protected $signature = 'simple-weather:forecast';

    protected $description = 'Updates the forecasts for the locations in your simple-weather config file';

    public function handle()
    {
        $locations = collect(config('simple-weather.locations', []));

        $locations->each(function($location) {
            $l = new Location($location['name'], $location['lat'], $location['long'], $location['identifier']);

            app('weather')->updateForecast($l);
        });
    }
}