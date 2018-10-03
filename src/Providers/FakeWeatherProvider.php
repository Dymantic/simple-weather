<?php

namespace Dymantic\SimpleWeather\Providers;

use Dymantic\SimpleWeather\ForecastDay;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\WeatherProvider;
use Illuminate\Support\Carbon;

class FakeWeatherProvider implements WeatherProvider
{

    public $currentTemp = 30;
    public $currentCondition = 'Sunny';

    public function rawFakeArray()
    {
        return [
            [
                'date' => Carbon::today()->addDay()->format('Y-m-d'),
                'min' => 20,
                'max' => 30,
                'condition' => 'Partly cloudy'
            ],
            [
                'date' => Carbon::today()->addDays(2)->format('Y-m-d'),
                'min' => 20,
                'max' => 30,
                'condition' => 'Partly cloudy'
            ],
            [
                'date' => Carbon::today()->addDays(3)->format('Y-m-d'),
                'min' => 20,
                'max' => 30,
                'condition' => 'Partly cloudy'
            ],
            [
                'date' => Carbon::today()->addDays(4)->format('Y-m-d'),
                'min' => 20,
                'max' => 30,
                'condition' => 'Partly cloudy'
            ],
            [
                'date' => Carbon::today()->addDays(5)->format('Y-m-d'),
                'min' => 20,
                'max' => 30,
                'condition' => 'Partly cloudy'
            ]
        ];
    }

    public function forecast(Location $location)
    {
        return $this->rawFakeArray();
    }

    public function current(Location $location)
    {
        return [
            'record_date' => Carbon::now(),
            'temp' => $this->currentTemp,
            'condition' => $this->currentCondition
        ];
    }
}