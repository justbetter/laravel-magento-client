<?php

namespace JustBetter\MagentoClient\Jobs\Middleware;

use Closure;
use JustBetter\MagentoClient\Client\Magento;

class AvailableMiddleware
{
    protected string $connection;

    protected int $seconds;

    public function __construct(?string $connection = null, int $seconds = 5)
    {
        $this->connection = $connection ?? config('magento.connection');
        $this->seconds = $seconds;
    }

    public function handle(object $job, Closure $next): void
    {
        /** @var Magento $magento */
        $magento = app(Magento::class);
        $magento->connection($this->connection);

        if ($magento->available()) {
            $next($job);
        } elseif (method_exists($job, 'release')) {
            $job->release($this->seconds);
        }
    }
}
