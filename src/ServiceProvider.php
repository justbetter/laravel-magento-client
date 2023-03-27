<?php

namespace JustBetter\MagentoClient;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JustBetter\MagentoClient\Actions\BuildRequest;
use JustBetter\MagentoClient\Actions\OAuth\RetrieveAccessToken;
use JustBetter\MagentoClient\Actions\OAuth\StoreIntegrationDetails;
use JustBetter\MagentoClient\Actions\RetrieveIntegrationToken;
use Illuminate\Support\Facades\Route;
use JustBetter\MagentoClient\Enums\AuthenticationMethod;

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

        RetrieveAccessToken::bind();
        StoreIntegrationDetails::bind();

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootRoutes();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/magento.php' => config_path('magento.php'),
        ], 'config');

        return $this;
    }

    protected function bootRoutes(): static
    {
        /** @var string $method */
        $method = config('magento.authentication_method');

        $authenticationMethod = AuthenticationMethod::from($method);

        if (! $this->app->routesAreCached() && $authenticationMethod === AuthenticationMethod::OAuth) {
            Route::middleware(config('magento.oauth.middleware'))
                ->prefix(config('magento.oauth.prefix'))
                ->group(__DIR__.'/../routes/web.php');
        }

        return $this;
    }
}
