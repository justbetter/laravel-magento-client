<?php

namespace JustBetter\MagentoClient;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use JustBetter\MagentoClient\Actions\AuthenticateRequest;
use JustBetter\MagentoClient\Actions\BuildRequest;
use JustBetter\MagentoClient\Actions\CheckMagento;
use JustBetter\MagentoClient\Actions\OAuth\RequestAccessToken;
use JustBetter\MagentoClient\Events\MagentoResponseEvent;
use JustBetter\MagentoClient\Http\Middleware\OAuthMiddleware;
use JustBetter\MagentoClient\Listeners\StoreAvailabilityListener;

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
        RequestAccessToken::bind();
        AuthenticateRequest::bind();
        BuildRequest::bind();
        CheckMagento::bind();

        return $this;
    }

    public function boot(): void
    {
        $this
            ->bootConfig()
            ->bootEvents()
            ->bootRoutes()
            ->bootMigrations();
    }

    protected function bootConfig(): static
    {
        $this->publishes([
            __DIR__.'/../config/magento.php' => config_path('magento.php'),
        ], 'config');

        return $this;
    }

    protected function bootEvents(): static
    {
        Event::listen(MagentoResponseEvent::class, StoreAvailabilityListener::class);

        return $this;
    }

    protected function bootRoutes(): static
    {
        if (! app()->routesAreCached()) {
            Route::prefix(config('magento.oauth.prefix'))
                ->middleware([OAuthMiddleware::class])
                ->group(__DIR__.'/../routes/web.php');
        }

        return $this;
    }

    protected function bootMigrations(): static
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }
}
