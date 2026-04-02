<?php

declare(strict_types=1);

namespace JustBetter\MagentoClient\Jobs\Middleware;

use Closure;
use JustBetter\MagentoClient\Client\Magento;

class AvailableMiddleware
{
    protected string $connection;

    public function __construct(?string $connection = null, protected int $seconds = 30)
    {
        $this->connection = $connection ?? config('magento.connection');
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
