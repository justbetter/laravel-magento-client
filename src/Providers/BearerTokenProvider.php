<?php

namespace JustBetter\MagentoClient\Providers;

use Illuminate\Http\Client\PendingRequest;

class BearerTokenProvider extends BaseProvider
{
    public function authenticate(PendingRequest $request): PendingRequest
    {
        /** @var string $token */
        $token = config('magento.access_token');

        return $request->withToken($token);
    }
}
