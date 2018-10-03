<?php


namespace Dymantic\SimpleWeather\Providers;


class ApixuConditions
{
    public static $conditions = [
        '1000' => 'sunny',
        '1003' => 'partly-cloudy',
        '1006' => 'cloudy',
        '1009' => 'overcast',
        '1030' => 'mist',
        '1063' => 'patchy-rain-possible',
        '1066' => 'patchy snow possible',
        '1069' => 'patchy sleet possible',
        '1072' => 'patchy-freezing-drizzle-possible',
        '1087' => 'thundery-outbreaks-possible',
        '1114' => 'blowing-snow',
        '1117' => 'blizzard',
        '1135' => 'fog',
        '1147' => 'freezing-fog',
        '1150' => 'patchy-light-drizzle',
        '1153' => 'light-drizzle',
        '1168' => 'freezing-drizzle',
        '1171' => 'heavy-freezing-drizzle',
        '1180' => 'patchy-light-rain',
        '1183' => 'light-rain',
        '1186' => 'moderate-rain-at-times',
        '1189' => 'moderate-rain',
        '1192' => 'heavy-rain-at-times',
        '1195' => 'heavy-rain',
        '1198' => 'light-freezing-rain',
        '1201' => 'moderate-or-heavy-freezing-rain',
        '1204' => 'light-sleet',
        '1207' => 'moderate-or-heavy-sleet',
        '1210' => 'patchy-light-snow',
        '1213' => 'light-snow',
        '1216' => 'patchy-moderate-snow',
        '1219' => 'moderate-snow',
        '1222' => 'patchy-heavy-snow',
        '1225' => 'heavy-snow',
        '1237' => 'ice-pellets',
        '1240' => 'light-rain-shower',
        '1243' => 'moderate-or-heavy-rain-shower',
        '1246' => 'torrential-rain-shower',
        '1249' => 'light-sleet-showers',
        '1252' => 'moderate-or-heavy-sleet-showers',
        '1255' => 'light-snow-showers',
        '1258' => 'moderate-or-heavy-snow-showers',
        '1261' => 'light-showers-of-ice-pellets',
        '1264' => 'moderate-or-heavy-showers-of-ice-pellets',
        '1273' => 'patchy-light rain with thunder',
        '1276' => 'moderate-or-heavy-rain-with-thunder',
        '1279' => 'patchy-light-snow-with-thunder',
        '1282' => 'moderate-or-heavy-snow-with-thunder',
        '9999' => 'unknown'
    ];
}
