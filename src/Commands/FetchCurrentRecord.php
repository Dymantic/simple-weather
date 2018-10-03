<?php


namespace Dymantic\SimpleWeather\Commands;


use Dymantic\SimpleWeather\Location;
use Illuminate\Console\Command;

class FetchCurrentRecord extends Command
{
    protected $signature = 'simple-weather:current';

    protected $description = 'Fetches and stores the current weather for the locations in your simple-weather config file';

    public function handle()
    {
        $locations = collect(config('simple-weather.locations', []));

        $locations->each(function($location) {
            $l = new Location($location['name'], $location['lat'], $location['long'], $location['identifier']);

            app('weather')->storeCurrent($l);
        });
    }
}