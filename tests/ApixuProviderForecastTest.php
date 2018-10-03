<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Exceptions\ForecastException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\ApixuProvider;
use GuzzleHttp\Psr7\Response;

class ApixuProviderForecastTest extends TestCase
{

    /**
     *@test
     */
    public function the_client_fetches_from_the_correct_url()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeApixuClient(new Response(200, [], '{"valid": "json"}'));
        $apixu = new ApixuProvider('TEST_KEY', $client);

        $apixu->forecast($test_location);

        $expected_url = "http://api.apixu.com/v1/forecast.json?key=TEST_KEY&q=24.333,121.7999&days=10";

        $this->assertEquals($expected_url, $client->fetchedFrom);
    }
    /**
     * @test
     */
    public function it_provides_the_forecast_in_the_correct_format_for_a_given_200_response()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeApixuClient(new Response(200, [],
            file_get_contents(__DIR__ . '/fixtures/apixu_forecast_200.json')));
        $apixu = new ApixuProvider(null, $client);

        $forecast = $apixu->forecast($test_location);

        $expected = [
            [
                'date' => '2018-10-02',
                'max' => '30.4',
                'min' => '23.2',
                'condition' => 'partly-cloudy'
            ],
            [
                'date' => '2018-10-03',
                'max' => '29.8',
                'min' => '23.1',
                'condition' => 'partly-cloudy'
            ],
            [
                'date' => '2018-10-04',
                'max' => '27.6',
                'min' => '22.2',
                'condition' => 'partly-cloudy'
            ],
            [
                'date' => '2018-10-05',
                'max' => '28',
                'min' => '25.2',
                'condition' => 'cloudy'
            ],
            [
                'date' => '2018-10-06',
                'max' => '30.8',
                'min' => '25.4',
                'condition' => 'partly-cloudy'
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
        $client = new FakeApixuClient(new Response(500));
        $apixu = new ApixuProvider(null, $client);

        try {
            $forecast = $apixu->forecast($test_location);

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
        $client = new FakeApixuClient(new \Exception());
        $apixu = new ApixuProvider(null, $client);

        try {
            $forecast = $apixu->forecast($test_location);

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
        $client = new FakeApixuClient(new Response(200, [], "{not: a json string}"));
        $apixu = new ApixuProvider(null, $client);

        try {
            $forecast = $apixu->forecast($test_location);

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