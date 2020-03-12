<?php


namespace Dymantic\SimpleWeather\Tests;



use GuzzleHttp\Psr7\Response;

class FakeTestClient
{

    public $fetchedFrom;

    private $response;

    public function __construct($response)
    {
        $this->response = $response;
    }
    public function get($url)
    {
        $this->fetchedFrom = $url;

        if($this->response instanceof \Exception) {
            throw $this->response;
        }
        return $this->response;
    }
}