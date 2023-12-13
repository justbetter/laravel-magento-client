<?php

namespace JustBetter\MagentoClient\Tests\Client;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Tests\TestCase;

class MultiConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Magento::fake();

        config()->set('magento.connections.otherconnection', [
            'base_url' => 'otherconnection',
            'base_path' => 'rest',
            'store_code' => 'all',
            'version' => 'V1',
            'access_token' => '::token::',
            'timeout' => 30,
            'connect_timeout' => 10,
            'authentication_method' => 'token',
        ]);
    }

    /** @test */
    public function it_can_do_requests_to_multiple_connections(): void
    {
        Http::fake([
            'magento*' => Http::response(),
            'otherconnection*' => Http::response(),
        ])->preventStrayRequests();

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $magento->connection('default')->get('products');
        $magento->connection('otherconnection')->get('products');

        Http::assertSentInOrder([
            fn (Request $request): bool => $request->url() === 'magento/rest/all/V1/products',
            fn (Request $request): bool => $request->url() === 'otherconnection/rest/all/V1/products',
        ]);
    }
}
