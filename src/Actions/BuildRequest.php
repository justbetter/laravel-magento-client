<?php

namespace JustBetter\MagentoClient\Actions;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Contracts\BuildsRequest;
use JustBetter\MagentoClient\Contracts\RetrievesBearerToken;

class BuildRequest implements BuildsRequest
{
    public function __construct(
        protected RetrievesBearerToken $bearerToken
    ) {
    }

    public function build(): PendingRequest
    {
        return Http::baseUrl(config('magento.base_url'))
            ->withToken($this->bearerToken->retrieve())
            ->timeout(config('magento.timeout'))
            ->connectTimeout(config('magento.connect_timeout'))
            ->acceptJson()
            ->asJson();
    }

    public static function bind(): void
    {
        app()->singleton(BuildsRequest::class, static::class);
    }
}
