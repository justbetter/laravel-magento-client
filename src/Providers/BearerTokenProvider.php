<?php

namespace JustBetter\MagentoClient\Providers;

use Illuminate\Http\Client\PendingRequest;

class BearerTokenProvider extends BaseProvider
{
    public function authenticate(string $connection, PendingRequest $request): PendingRequest
    {
        /** @var string $token */
        $token = config('magento.connections.'.$connection.'.access_token');

        return $request->withToken($token);
    }
}
