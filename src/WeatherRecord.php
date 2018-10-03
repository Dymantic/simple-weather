<?php


namespace Dymantic\SimpleWeather;


use Illuminate\Database\Eloquent\Model;

class WeatherRecord extends Model
{
    protected $table = 'simple_weather';

    protected $fillable = ['record_date', 'temp', 'condition', 'location_identifier'];

    protected $dates = ['record_date'];
}