<?php


namespace Dymantic\SimpleWeather\Tests;


use Dymantic\SimpleWeather\Exceptions\CurrentUpdateException;
use Dymantic\SimpleWeather\Location;
use Dymantic\SimpleWeather\Providers\ApixuProvider;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Carbon;

class ApixuProviderCurrentTest extends TestCase
{

    /**
     *@test
     */
    public function it_fetches_from_the_correct_url()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeApixuClient(new Response(200, [], '{"valid": "json"}'));
        $apixu = new ApixuProvider('TEST_KEY', $client);

        $apixu->current($test_location);

        $expected_url = "http://api.apixu.com/v1/current.json?key=TEST_KEY&q=24.333,121.7999";

        $this->assertEquals($expected_url, $client->fetchedFrom);
    }

    /**
     *@test
     */
    public function it_returns_correctly_formatted_data_for_a_200_response()
    {
        $test_location = $this->fakeLocation();
        $client = new FakeApixuClient(new Response(200, [],
            file_get_contents(__DIR__ . '/fixtures/apixu_current_200.json')));
        $apixu = new ApixuProvider(null, $client);

        $current = $apixu->current($test_location);

        $expected = [
            'record_date' => Carbon::parse("2018-10-03 09:30"),
            'temp' => '25',
            'condition' => 'partly-cloudy',
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
        $client = new FakeApixuClient(new Response(500));
        $apixu = new ApixuProvider(null, $client);

        try {
            $apixu->current($test_location);

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
        $client = new FakeApixuClient(new \Exception());
        $apixu = new ApixuProvider(null, $client);

        try {
            $apixu->current($test_location);

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
        $client = new FakeApixuClient(new Response(200, [], "{not: a json string}"));
        $apixu = new ApixuProvider(null, $client);

        try {
            $apixu->current($test_location);

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