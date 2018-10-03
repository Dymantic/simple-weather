<?php


namespace Dymantic\SimpleWeather\Providers;


use Dymantic\SimpleWeather\Exceptions\CurrentUpdateException;
use Dymantic\SimpleWeather\Exceptions\ForecastException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\WeatherProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ApixuProvider implements WeatherProvider
{

    private $client;
    private $key;

    public function __construct($key, $client)
    {
        $this->client = $client;
        $this->key = $key;
    }

    public function forecast(Location $location)
    {
        $coords = "{$location->lat},{$location->long}";
        $url = "http://api.apixu.com/v1/forecast.json?key={$this->key}&q={$coords}&days=10";

        try {
            $resp = $this->client->get($url);
        } catch(\Exception $e) {
            throw new ForecastException($e->getMessage());
        }

        if($resp->getStatusCode() >= 300) {
            throw new ForecastException('failed to fetch forecast');
        }

        $data = json_decode($resp->getBody()->getContents(), true);

        if(! $data) {
            throw new ForecastException('failed to parse forecast json');
        }

        return collect(Arr::get($data, "forecast.forecastday", []))->map(function($day) {
            $code = Arr::get($day, 'day.condition.code', '9999');
            return [
                'date' => Arr::get($day, 'date', '1999-12-31'),
                'min' => (string)Arr::get($day, 'day.mintemp_c', ''),
                'max' => (string)Arr::get($day, 'day.maxtemp_c', ''),
                'condition' => (string)Arr::get(ApixuConditions::$conditions, $code, 'unknown')
            ];
        })->all();
    }

    public function current(Location $location)
    {
        $coords = "{$location->lat},{$location->long}";
        $url = "http://api.apixu.com/v1/current.json?key={$this->key}&q={$coords}";

        try {
            $resp = $this->client->get($url);
        } catch(\Exception $e) {
            throw new CurrentUpdateException($e->getMessage());
        }

        if($resp->getStatusCode() >= 300) {
            throw new CurrentUpdateException('failed to fetch current weather');
        }

        $data = json_decode($resp->getBody()->getContents(), true);

        if(! $data) {
            throw new CurrentUpdateException('failed to parse update json');
        }

        $code = (string)Arr::get($data, 'current.condition.code', '9999');

        return [
            'record_date' => Carbon::parse(Arr::get($data, 'current.last_updated')),
            'temp' => (string)Arr::get($data, 'current.temp_c', ''),
            'condition' => Arr::get(ApixuConditions::$conditions, $code, 'unknown'),
            'location_identifier' => $location->identifier
        ];
    }
}