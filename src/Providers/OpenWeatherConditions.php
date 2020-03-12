<?php


namespace Dymantic\SimpleWeather\Providers;


use Illuminate\Support\Arr;

class OpenWeatherConditions
{
    public static $conditions = [
        '200' => 'thundery-outbreaks-possible',
        '201' => 'thundery-outbreaks-possible',
        '202' => 'thundery-outbreaks-possible',
        '210' => 'thundery-outbreaks-possible',
        '211' => 'thundery-outbreaks-possible',
        '212' => 'thundery-outbreaks-possible',
        '221' => 'thundery-outbreaks-possible',
        '230' => 'thundery-outbreaks-possible',
        '231' => 'thundery-outbreaks-possible',
        '232' => 'thundery-outbreaks-possible',

        '300' => 'light-rain',
        '301' => 'light-rain',
        '302' => 'light-rain',
        '310' => 'light-rain',
        '311' => 'light-rain',
        '312' => 'light-rain',
        '313' => 'light-rain',
        '314' => 'light-rain',
        '321' => 'light-rain',

        '500' => 'light-rain',
        '501' => 'moderate-rain',
        '502' => 'heavy-rain',
        '503' => 'heavy-rain',
        '504' => 'heavy-rain',
        '511' => 'moderate-or-heavy-freezing-rain',
        '520' => 'light-rain-shower',
        '521' => 'patchy-rain-possible',
        '522' => 'patchy-rain-possible',
        '531' => 'patchy-rain-possible',

        '600' => 'light-snow',
        '601' => 'light-snow',
        '602' => 'light-snow',
        '611' => 'light-snow',
        '612' => 'light-snow',
        '613' => 'light-snow',
        '615' => 'light-snow',
        '616' => 'light-snow',
        '620' => 'light-snow',
        '621' => 'light-snow',
        '622' => 'light-snow',

        '701' => 'mist',
        '711' => 'smoke',
        '721' => 'haze',
        '731' => 'dust',
        '741' => 'fog',
        '751' => 'sand',
        '761' => 'dust',
        '762' => 'ash',
        '771' => 'squall',
        '781' => 'tornado',

        '800' => 'sunny',
        '801' => 'sunny',
        '802' => 'partly-cloudy',
        '803' => 'partly-cloudy',
        '804' => 'cloudy',
    ];

    public static function forCode($code)
    {
        return Arr::get(static::$conditions, $code, 'unknown');
    }
}