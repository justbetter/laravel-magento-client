<?php

namespace JustBetter\MagentoClient\Actions;

use Illuminate\Http\Client\PendingRequest;
use JustBetter\MagentoClient\Contracts\AuthenticatesRequest;
use JustBetter\MagentoClient\Enums\AuthenticationMethod;

class AuthenticateRequest implements AuthenticatesRequest
{
    public function authenticate(PendingRequest $request, string $connection): PendingRequest
    {
        /** @var string $method */
        $method = config('magento.connections.'.$connection.'.authentication_method');

        $auth = AuthenticationMethod::from($method);

        return $auth->provider()->authenticate($request, $connection);
    }

    public static function bind(): void
    {
        app()->singleton(AuthenticatesRequest::class, static::class);
    }
}
