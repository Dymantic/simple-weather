<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Exceptions\ForecastException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\ApixuProvider;
use Dymantic\SimpleWeather\Providers\OpenWeatherProvider;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;

class OpenWeatherForecastTest extends TestCase
{

    /**
     *@test
     */
    public function the_client_fetches_from_the_correct_url()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(200, [], '{"valid": "json"}'));
        $open_weather = new OpenWeatherProvider('TEST_KEY', $client);

        $open_weather->forecast($test_location);

        $expected_url = "http://api.openweathermap.org/data/2.5/forecast?lat=24.333&lon=121.7999&units=metric&APPID=TEST_KEY";

        $this->assertEquals($expected_url, $client->fetchedFrom);
    }

    /**
     *@test
     */
    public function it_gets_the_correct_forecast_data()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new Response(200, [],
            file_get_contents(__DIR__ . '/fixtures/open_weather_forecast_200.json')));
        $open_weather = new OpenWeatherProvider(null, $client);

        $forecast = $open_weather->forecast($test_location);

        $expected = [
            [
                'date' => '2020-03-12',
                'max' => '5.85',
                'min' => '5.2',
                'condition' => 'cloudy'
            ],
            [
                'date' => '2020-03-13',
                'max' => '3.75',
                'min' => '3.75',
                'condition' => 'partly-cloudy'
            ],
            [
                'date' => '2020-03-14',
                'max' => '6.25',
                'min' => '6.25',
                'condition' => 'cloudy'
            ],
            [
                'date' => '2020-03-15',
                'max' => '6.79',
                'min' => '6.79',
                'condition' => 'cloudy'
            ],
            [
                'date' => '2020-03-16',
                'max' => '4.35',
                'min' => '4.35',
                'condition' => 'sunny'
            ]
        ];

        $this->assertEquals($expected, $forecast);
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
            $forecast = $open_weather->forecast($test_location);

            $this->fail('expected a ForecastException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(ForecastException::class, $e);
        }
    }

    /**
     *@test
     */
    public function an_exception_will_be_rethrown_as_a_forecast_exception()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeTestClient(new \Exception());
        $open_weather = new OpenWeatherProvider(null, $client);

        try {
            $forecast = $open_weather->forecast($test_location);

            $this->fail('expected a ForecastException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(ForecastException::class, $e);
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
            $forecast = $open_weather->forecast($test_location);

            $this->fail('expected a ForecastException to be thrown');
        } catch(\Exception $e) {
            $this->assertInstanceOf(ForecastException::class, $e);
        }
    }

    private function fakeLocation()
    {
        return new Location('test', '24.333', '121.7999', 'test_id');
    }
}