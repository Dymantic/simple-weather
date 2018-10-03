<?php


namespace Dymantic\SimpleWeather;


class ForecastDay
{
    public $date;
    public $min;
    public $max;
    public $condition;

    public function __construct($attributes)
    {
        $this->date = $attributes['date'];
        $this->min = $attributes['min'];
        $this->max = $attributes['max'];
        $this->condition = $attributes['condition'];
    }

    public function toArray()
    {
        return [
            'date' => $this->date,
            'min' => $this->min,
            'max' => $this->max,
            'condition' => $this->condition,
        ];
    }
}