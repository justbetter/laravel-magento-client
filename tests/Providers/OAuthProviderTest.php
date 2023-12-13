<?php

namespace JustBetter\MagentoClient\Tests\Providers;

use Exception;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore;
use JustBetter\MagentoClient\Providers\OAuthProvider;
use JustBetter\MagentoClient\Tests\TestCase;
use Mockery\MockInterface;
use Psr\Http\Message\RequestInterface;

class OAuthProviderTest extends TestCase
{
    /** @test */
    public function it_can_authenticate_requests(): void
    {
        $this->mock(FileKeyStore::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('get')
                ->once()
                ->andReturn([
                    'oauth_consumer_key' => '::oauth-consumer-key::',
                    'oauth_consumer_secret' => '::oauth-consumer-secret::',
                    'oauth_verifier' => '::oauth-verifier::',
                    'access_token' => '::access-token::',
                    'access_token_secret' => '::access-token-secret::',
                ]);
        });

        $pendingRequest = Http::baseUrl('localhost');

        /** @var OAuthProvider $provider */
        $provider = app(OAuthProvider::class);
        $provider->authenticate($pendingRequest, 'default');

        $authorization = null;

        try {
            $pendingRequest->withMiddleware(
                Middleware::mapRequest(function (RequestInterface $request) use (&$authorization) {
                    $authorization = $request->getHeader('Authorization');

                    throw new Exception('Cancel request execution');
                })
            )->get('/', [
                'key' => 'value',
            ]);
        } catch (Exception) {
            //
        }

        if ($authorization === null) {
            $this->fail('No authorization header has been set');
        }

        $authorization = $authorization[0];

        if (! preg_match_all('/(?<keys>[a-z_]+)(?>=)/', $authorization, $matches)) {
            $this->fail('Could not match any keys');
        }

        $this->assertEquals([
            'oauth_consumer_key',
            'oauth_nonce',
            'oauth_signature_method',
            'oauth_timestamp',
            'oauth_version',
            'oauth_verifier',
            'oauth_token',
            'oauth_signature',
        ], $matches['keys']);
    }
}
