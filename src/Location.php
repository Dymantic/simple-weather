<?php


namespace Dymantic\SimpleWeather;


class Location
{
    public $name;
    public $lat;
    public $long;
    public $identifier;

    public function __construct($name, $lat, $long, $identifier)
    {
        $this->name = $name;
        $this->lat = $lat;
        $this->long = $long;
        $this->identifier = $identifier;
    }

    public function slug()
    {
        return str_slug($this->name);
    }
}