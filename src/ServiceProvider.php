<?php

namespace JustBetter\MagentoClient;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JustBetter\MagentoClient\Actions\BuildRequest;
use JustBetter\MagentoClient\Actions\RetrieveIntegrationToken;

class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this
            ->registerConfig()
            ->registerActions();
    }

    protected function registerConfig(): static
    {
        $this->mergeConfigFrom(__DIR__.'/../config/magento.php', 'magento');

        return $this;
    }

    protected function registerActions(): static
    {
        RetrieveIntegrationToken::bind();
        BuildRequest::bind();

        return $this;
    }

    public function boot(): void
    {
        $this->bootConfig();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/magento.php' => config_path('magento.php'),
        ], 'config');

        return $this;
    }
}
