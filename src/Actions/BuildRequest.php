<?php

namespace JustBetter\MagentoClient\Actions;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Contracts\AuthenticatesRequest;
use JustBetter\MagentoClient\Contracts\BuildsRequest;

class BuildRequest implements BuildsRequest
{
    public function __construct(
        protected AuthenticatesRequest $request
    ) {}

    public function build(string $connection): PendingRequest
    {
        /** @var array $options */
        $options = config('magento.connections.'.$connection);

        $pendingRequest = Http::baseUrl($options['base_url'])
            ->timeout($options['timeout'])
            ->connectTimeout($options['connect_timeout'])
            ->acceptJson()
            ->asJson();

        return $this->request->authenticate($pendingRequest, $connection);
    }

    public static function bind(): void
    {
        app()->singleton(BuildsRequest::class, static::class);
    }
}
