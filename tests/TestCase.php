<?php


namespace Dymantic\SimpleWeather\Tests;

use Dymantic\SimpleWeather\SimpleWeatherServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;


class TestCase extends Orchestra
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageProviders($app)
    {
        return [SimpleWeatherServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    private function setUpDatabase()
    {
        include_once __DIR__ . '/../database/migrations/create_simple_weather_table.php.stub';
        (new \CreateSimpleWeatherTable())->up();
    }
}