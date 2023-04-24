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
    ) {
    }

    public function build(): PendingRequest
    {
        $pendingRequest = Http::baseUrl(config('magento.base_url'))
            ->timeout(config('magento.timeout'))
            ->connectTimeout(config('magento.connect_timeout'))
            ->acceptJson()
            ->asJson();

        return $this->request->authenticate($pendingRequest);
    }

    public static function bind(): void
    {
        app()->singleton(BuildsRequest::class, static::class);
    }
}
