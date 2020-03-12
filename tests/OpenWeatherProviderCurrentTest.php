<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Exceptions\CurrentUpdateException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\ApixuProvider;
use Dymantic\SimpleWeather\Providers\OpenWeatherProvider;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;

class OpenWeatherProviderCurrentTest extends TestCase
{
    /**
     *@test
     */
    public function it_calls_the_correct_url()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(200, [], '{"valid": "json"}'));
        $openWeather = new OpenWeatherProvider('TEST_KEY', $client);

        $openWeather->current($test_location);

        $expected_url = "http://api.openweathermap.org/data/2.5/weather?lat=24.333&lon=121.7999&units=metric&APPID=TEST_KEY";

        $this->assertEquals($expected_url, $client->fetchedFrom);
    }

    /**
     *@test
     */
    public function it_returns_correctly_formatted_data_for_a_200_response()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(200, [],
            file_get_contents(__DIR__ . '/fixtures/openweather_current_200.json')));
        $open_weather = new OpenWeatherProvider(null, $client);

        $current = $open_weather->current($test_location);

        $expected = [
            'record_date' => Carbon::parse(1560350192),
            'temp' => '31.4',
            'condition' => 'sunny',
            'location_identifier' => $test_location->identifier
        ];

        $this->assertEquals($expected, $current);
    }

    /**
     *@test
     */
    public function a_non_200_response_will_throw_an_exception()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(500));
        $open_weather = new OpenWeatherProvider(null, $client);

        try {
            $open_weather->current($test_location);

            $this->fail('expected a CurrentUpdateException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(CurrentUpdateException::class, $e);
        }
    }

    /**
     *@test
     */
    public function any_client_exception_will_be_rethrown_as_current_update_exception()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new \Exception());
        $open_weather = new OpenWeatherProvider(null, $client);

        try {
            $open_weather->current($test_location);

            $this->fail('expected a CurrentUpdateException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(CurrentUpdateException::class, $e);
        }
    }

    /**
     *@test
     */
    public function bad_json_will_result_in_forecast_exception()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(200, [], "{not: a json string}"));
        $open_weather = new OpenWeatherProvider(null, $client);

        try {
            $open_weather->current($test_location);

            $this->fail('expected a CurrentUpdateException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(CurrentUpdateException::class, $e);
        }
    }

    private function fakeLocation()
    {
        return new Location('test', '24.333', '121.7999', 'test_id');
    }
}