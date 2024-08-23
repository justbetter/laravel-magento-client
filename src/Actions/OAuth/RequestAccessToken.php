<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use Illuminate\Support\Facades\Validator;
use JustBetter\MagentoClient\Contracts\OAuth\RequestsAccessToken;
use JustBetter\MagentoClient\OAuth\HmacSha256Signature;
use JustBetter\MagentoClient\OAuth\KeyStore\KeyStore;
use JustBetter\MagentoClient\OAuth\MagentoServer;
use League\OAuth1\Client\Credentials\ClientCredentials;

class RequestAccessToken implements RequestsAccessToken
{
    public function request(string $connection, string $key): void
    {
        $keys = KeyStore::instance()->get($connection);

        $callback = $keys['callback'] ?? [];

        Validator::validate($callback, [
            'oauth_consumer_key' => 'required|string',
            'oauth_consumer_secret' => 'required|string',
            'oauth_verifier' => 'required|string',
        ]);

        if ($callback['oauth_consumer_key'] !== $key) {
            abort(403);
        }

        $credentials = new ClientCredentials;
        $credentials->setIdentifier($callback['oauth_consumer_key']);
        $credentials->setSecret($callback['oauth_consumer_secret']);
        $credentials->setCallbackUri(route('magento.oauth.callback', ['connection' => $connection]));

        /** @var MagentoServer $server */
        $server = app()->makeWith(MagentoServer::class, [
            'clientCredentials' => $credentials,
            'signature' => new HmacSha256Signature($credentials),
        ]);

        $server->connection = $connection;

        $temporaryCredentials = $server->getTemporaryCredentials();

        $tokenCredentials = $server->getTokenCredentials(
            $temporaryCredentials,
            $temporaryCredentials->getIdentifier(),
            $callback['oauth_verifier']
        );

        $auth['access_token'] = $tokenCredentials->getIdentifier();
        $auth['access_token_secret'] = $tokenCredentials->getSecret();

        KeyStore::instance()->set(
            $connection,
            array_merge($callback, $auth),
        );
    }

    public static function bind(): void
    {
        app()->singleton(RequestsAccessToken::class, static::class);
    }
}
