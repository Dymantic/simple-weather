<?php


namespace Dymantic\SimpleWeather\Providers;


use Dymantic\SimpleWeather\Exceptions\CurrentUpdateException;
use Dymantic\SimpleWeather\Exceptions\ForecastException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\WeatherProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class OpenWeatherProvider implements WeatherProvider
{

    private $key;
    private $client;

    public function __construct($key, $client)
    {
        $this->key = $key;
        $this->client = $client;
    }

    public function forecast(Location $location)
    {
        $url = sprintf(
            "http://api.openweathermap.org/data/2.5/forecast?lat=%s&lon=%s&units=metric&APPID=%s",
            $location->lat,
            $location->long,
            $this->key
        );

        try {
            $data = $this->getJson($url);
        } catch(\Exception $e) {
            throw new ForecastException($e->getMessage());
        }

        if(!($data['list'] ?? false)) {
            return [];
        }

        $days = collect($data['list'])
            ->filter(function($time_data) {
                $time = Carbon::parse($time_data['dt']);
                return $time->hour >= 11 && $time->hour <= 14;
            })
            ->map(function($day) {
                $code = $day['weather'][0]['id'] ?? '9999';
                return [
                    'date' => Carbon::parse($day['dt'])->format('Y-m-d'),
                    'max' => Arr::get($day, 'main.temp_max'),
                    'min' => Arr::get($day, 'main.temp_min'),
                    'condition' => OpenWeatherConditions::forCode($code),
                ];
            })->values()->all();

        return $days;
    }

    public function current(Location $location)
    {
        $url = sprintf("http://api.openweathermap.org/data/2.5/weather?lat=%s&lon=%s&units=metric&APPID=%s", $location->lat, $location->long, $this->key);

        try {
            $data = $this->getJson($url);
        } catch(\Exception $e) {
            throw new CurrentUpdateException($e->getMessage());
        }

        return [
            'record_date' => Carbon::parse(Arr::get($data, 'dt')),
            'temp' => (string)Arr::get($data, 'main.temp', ''),
            'condition' => OpenWeatherConditions::forCode($data['weather'][0]['id'] ?? '9999'),
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