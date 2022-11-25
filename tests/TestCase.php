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
}
