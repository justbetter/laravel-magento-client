<?php

namespace JustBetter\MagentoClient\Tests\Providers;

use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Providers\BearerTokenProvider;
use JustBetter\MagentoClient\Tests\TestCase;

class BearerTokenProviderTest extends TestCase
{
    /** @test */
    public function it_can_authenticate_requests(): void
    {
        config('magento.connections.default.token', '::token::');

        $pendingRequest = Http::baseUrl('localhost');

        /** @var BearerTokenProvider $provider */
        $provider = app(BearerTokenProvider::class);
        $provider->authenticate($pendingRequest, 'default');

        $options = $pendingRequest->getOptions();

        $authorization = data_get($options, 'headers.Authorization');

        $this->assertEquals($authorization, 'Bearer ::token::');
    }
}
