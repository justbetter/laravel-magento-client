<?php

namespace JustBetter\MagentoClient\Tests;

use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\ServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        Magento::fake();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate',
            ['--database' => 'testbench'])->run();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
