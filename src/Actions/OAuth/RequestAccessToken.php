<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use JustBetter\MagentoClient\Contracts\OAuth\RequestsAccessToken;
use JustBetter\MagentoClient\OAuth\HmacSha256Signature;
use JustBetter\MagentoClient\OAuth\MagentoServer;
use League\OAuth1\Client\Credentials\ClientCredentials;

class RequestAccessToken implements RequestsAccessToken
{
    public function __construct(
        protected ManageKeys $keys
    ) {
    }

    public function request(array $data): void
    {
        $keys = $this->keys->get();

        if (! isset($keys['oauth_consumer_key']) || $keys['oauth_consumer_key'] !== $data['oauth_consumer_key']) {
            abort(403);
        }

        $this->keys->set($data);

        $keys = $data;

        $credentials = new ClientCredentials();
        $credentials->setIdentifier($keys['oauth_consumer_key']);
        $credentials->setSecret($keys['oauth_consumer_secret']);
        $credentials->setCallbackUri(route('magento.oauth.callback'));

        $server = new MagentoServer($credentials, new HmacSha256Signature($credentials));

        $temporaryCredentials = $server->getTemporaryCredentials();

        $keys['request_token'] = $temporaryCredentials->getIdentifier();
        $keys['request_token_secret'] = $temporaryCredentials->getSecret();

        $tokenCredentials = $server->getTokenCredentials(
            $temporaryCredentials,
            $temporaryCredentials->getIdentifier(),
            $keys['oauth_verifier']
        );

        $keys['access_token'] = $tokenCredentials->getIdentifier();
        $keys['access_token_secret'] = $tokenCredentials->getSecret();

        $this->keys->set($keys);
    }

    public static function bind(): void
    {
        app()->singleton(RequestsAccessToken::class, static::class);
    }
}
