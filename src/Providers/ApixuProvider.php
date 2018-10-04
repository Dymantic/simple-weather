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
        $url = "http://api.apixu.com/v1/forecast.json?key={$this->key}&q={$location->coords()}&days=10";

        try {
            $data = $this->getJson($url);
        } catch(\Exception $e) {
            throw new ForecastException($e->getMessage());
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
        $url = "http://api.apixu.com/v1/current.json?key={$this->key}&q={$location->coords()}";

        try {
            $data = $this->getJson($url);
        } catch(\Exception $e) {
            throw new CurrentUpdateException($e->getMessage());
        }

        $code = (string)Arr::get($data, 'current.condition.code', '9999');

        return [
            'record_date' => Carbon::parse(Arr::get($data, 'current.last_updated')),
            'temp' => (string)Arr::get($data, 'current.temp_c', ''),
            'condition' => Arr::get(ApixuConditions::$conditions, $code, 'unknown'),
            'location_identifier' => $location->identifier
        ];
    }

    private function getJson($url)
    {
        $resp = $this->client->get($url);
        if($resp->getStatusCode() >= 300) {
            throw new \Exception('failed to fetch current weather');
        }
        $json = json_decode($resp->getBody()->getContents(), true);

        if(! $json) {
            throw new \Exception('failed to parse apixu json response');
        }

        return $json;
    }
}