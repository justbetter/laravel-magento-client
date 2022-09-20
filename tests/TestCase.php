<?php

namespace JustBetter\MagentoClient\Tests;

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
        $app['config']->set('magento.base_path', 'rest');
        $app['config']->set('magento.store_code', 'all');
        $app['config']->set('magento.version', 'V1');
        $app['config']->set('magento.timeout', 30);
        $app['config']->set('magento.connect_timeout', 30);
        $app['config']->set('magento.base_url', 'http://magento.test');
        $app['config']->set('magento.access_token', 'secure-token');
    }
}
