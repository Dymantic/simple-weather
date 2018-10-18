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

        $today = $this->normalizedToday($cached);

        if(!$today) {
            return [
                'location' => ['name' => $location->name],
                'days'     => []
            ];
        }

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

        if(!$today) {
            return;
        }

        $day = is_array($today) ? $this->formatFromArray($today) : $this->formatFromObject($today);
        $day['is_today'] = true;

        return $day;


    }

    private function pastDays($location)
    {
        $days = WeatherRecord::where('location_identifier', $location->identifier)
                            ->where('record_date', '<', Carbon::today()->startOfDay())
                            ->orderBy('record_date', 'desc')
                            ->limit(3)
                            ->get();

        return $days->reverse()->values()->map(function($day) {
            return $this->formatFromObject($day);
        })->all();
    }

    private function futureDays($cached)
    {
        $days = $cached->reject(function ($day) {
            return Carbon::parse($day['date'])->lt(Carbon::today()->endOfDay());
        })->take(3);

        return $days->map(function($day) {
            return $this->formatFromArray($day);
        })->all();
    }

    private function formatFromArray($day)
    {
        return [
            'date'      => $day['date'],
            'day_name'  => Carbon::parse($day['date'])->format('l'),
            'day_name_short'  => Carbon::parse($day['date'])->format('D'),
            'is_today'  => false,
            'temp'      => intval(round(floatval($day['max']))),
            'condition' => $day['condition']
        ];
    }

    private function formatFromObject($day)
    {
        return [
            'date'      => $day->record_date->format('Y-m-d'),
            'day_name'  => $day->record_date->format('l'),
            'day_name_short'  => $day->record_date->format('D'),
            'is_today'  => false,
            'temp'      => intval(round(floatval($day->temp))),
            'condition' => $day->condition
        ];
    }
}