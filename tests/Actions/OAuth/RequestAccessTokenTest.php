<?php

namespace JustBetter\MagentoClient\Tests\Client;

use JustBetter\MagentoClient\Actions\OAuth\RequestAccessToken;
use JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore;
use JustBetter\MagentoClient\OAuth\MagentoServer;
use JustBetter\MagentoClient\Tests\TestCase;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RequestAccessTokenTest extends TestCase
{
    /** @test */
    public function it_can_request_access_tokens(): void
    {
        $temporaryCredentials = new TemporaryCredentials;
        $temporaryCredentials->setIdentifier('::request-token::');
        $temporaryCredentials->setSecret('::request-token-secret::');

        $tokenCredentials = new TokenCredentials;
        $tokenCredentials->setIdentifier('::access-token::');
        $tokenCredentials->setSecret('::access-token-secret::');

        /** @var MagentoServer $server */
        $server = $this->mock(MagentoServer::class,
            function (MockInterface $mock) use ($temporaryCredentials, $tokenCredentials): void {
                $mock
                    ->shouldReceive('getTemporaryCredentials')
                    ->once()
                    ->andReturn($temporaryCredentials);

                $mock
                    ->shouldReceive('getTokenCredentials')
                    ->once()
                    ->andReturn($tokenCredentials);
            });

        app()->bind(MagentoServer::class, fn () => $server);

        $this->mock(FileKeyStore::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('get')
                ->once()
                ->andReturn([
                    'callback' => [
                        'oauth_consumer_key' => '::oauth-consumer-key::',
                        'oauth_consumer_secret' => '::oauth-consumer-secret::',
                        'oauth_verifier' => '::oauth-verifier::',
                    ],
                ]);

            $mock
                ->shouldReceive('set')
                ->once()
                ->with('default', [
                    'oauth_consumer_key' => '::oauth-consumer-key::',
                    'oauth_consumer_secret' => '::oauth-consumer-secret::',
                    'oauth_verifier' => '::oauth-verifier::',
                    'access_token' => '::access-token::',
                    'access_token_secret' => '::access-token-secret::',
                ]);
        });

        $key = '::oauth-consumer-key::';

        /** @var RequestAccessToken $action */
        $action = app(RequestAccessToken::class);
        $action->request('default', $key);
    }

    /** @test */
    public function it_can_throw_http_exceptions(): void
    {
        $this->expectException(HttpException::class);

        $this->mock(FileKeyStore::class, function (MockInterface $mock): void {
            $mock
                ->shouldReceive('get')
                ->once()
                ->andReturn([
                    'callback' => [
                        'oauth_consumer_key' => '::oauth-consumer-key::',
                        'oauth_consumer_secret' => '::oauth-consumer-secret::',
                        'oauth_verifier' => '::oauth-verifier::',
                    ],
                ]);
        });

        $key = '::different-key::';

        /** @var RequestAccessToken $action */
        $action = app(RequestAccessToken::class);
        $action->request('default', $key);
    }
}
