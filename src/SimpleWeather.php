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

    public function storeCurrent(Location $location)
    {
        $current = $this->provider->current($location);
        $this->removeExistingForTodayAndLocation($location);
        WeatherRecord::create($current);
    }

    private function removeExistingForTodayAndLocation($location)
    {
        $existing = WeatherRecord::where('location_identifier', $location->identifier)->where('record_date', '>=',
            Carbon::today()->startOfDay())->get();
        $existing->each(function ($record) {
            $record->delete();
        });
    }

    public function overview(Location $location)
    {
        $cached = collect(cache()->get('weather.forecast.' . $location->slug() . '.days', []))->sortBy('date');
        $past = $this->pastDays($location);
        $future = $this->futureDays($cached);

        $days = collect($past);

        $days->push($this->normalizedToday($cached));

        collect($future)->each(function ($day) use ($days) {
            $days->push($day);
        });

        return [
            'location' => ['name' => $location->name],
            'days'     => $days->all()
        ];
    }

    private function normalizedToday($cached)
    {
        $today = WeatherRecord::where('record_date', '>=', Carbon::today()->startOfDay())->latest()->first();

        if (!$today) {
            $today = $cached->first(function ($day) {
                return Carbon::parse($day['date'])->isToday();
            });
        }

        return [
            'date'      => is_array($today) ? $today['date'] : $today->record_date->format('Y-m-d'),
            'day_name'  => is_array($today) ? Carbon::parse($today['date'])->format('l') : $today->record_date->format('l'),
            'is_today'  => true,
            'temp'      => round(floatval(is_array($today) ? $today['max'] : $today->temp)),
            'condition' => is_array($today) ? $today['condition'] : $today->condition
        ];
    }

    private function pastDays($location)
    {
        $days = WeatherRecord::where('location_identifier', $location->identifier)
                            ->where('record_date', '<', Carbon::today()->startOfDay())
                            ->orderBy('record_date', 'desc')
                            ->limit(3)
                            ->get();

        return $days->reverse()->values()->map(function($day) {
            return [
                'date'      => $day->record_date->format('Y-m-d'),
                'day_name'  => $day->record_date->format('l'),
                'is_today'  => false,
                'temp'      => round(floatval($day->temp)),
                'condition' => $day->condition
            ];
        })->all();
    }

    private function futureDays($cached)
    {
        $days = $cached->reject(function ($day) {
            return Carbon::parse($day['date'])->isToday();
        })->take(3);

        return $days->map(function($day) {
            return [
                'date'      => $day['date'],
                'day_name'  => Carbon::parse($day['date'])->format('l'),
                'is_today'  => false,
                'temp'      => round(floatval($day['max'])),
                'condition' => $day['condition']
            ];
        })->all();
    }
}