<?php

namespace JustBetter\MagentoClient\Actions;

use Illuminate\Http\Client\PendingRequest;
use JustBetter\MagentoClient\Contracts\AuthenticatesRequest;
use JustBetter\MagentoClient\Enums\AuthenticationMethod;

class AuthenticateRequest implements AuthenticatesRequest
{
    public function authenticate(PendingRequest $request): PendingRequest
    {
        /** @var string $method */
        $method = config('magento.authentication_method');

        $auth = AuthenticationMethod::from($method);

        return $auth->provider()->authenticate($request);
    }

    public static function bind(): void
    {
        app()->singleton(AuthenticatesRequest::class, static::class);
    }
}
