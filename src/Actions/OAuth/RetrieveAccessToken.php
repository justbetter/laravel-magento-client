<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use Exception;
use JustBetter\MagentoClient\Concerns\InteractsWithOAuthSecretFile;
use JustBetter\MagentoClient\Contracts\OAuth\RetrievesAccessToken;
use JustBetter\MagentoClient\OAuth\HmacSha256Signature;
use JustBetter\MagentoClient\OAuth\MagentoServer;
use League\OAuth1\Client\Credentials\ClientCredentials;

class RetrieveAccessToken implements RetrievesAccessToken
{
    use InteractsWithOAuthSecretFile;

    public function retrieve(): void
    {
        $content = $this->read();

        if ($content === null) {
            throw new Exception('Secret file does not exist');
        }

        $credentials = new ClientCredentials();
        $credentials->setIdentifier($content['oauth_consumer_key']);
        $credentials->setSecret($content['oauth_consumer_secret']);
        $credentials->setCallbackUri(route('magento.oauth.callback'));

        $server = new MagentoServer($credentials, new HmacSha256Signature($credentials));

        $temporaryCredentials = $server->getTemporaryCredentials();

        $content['request_token'] = $temporaryCredentials->getIdentifier();
        $content['request_token_secret'] = $temporaryCredentials->getSecret();

        $tokenCredentials = $server->getTokenCredentials(
            $temporaryCredentials,
            $temporaryCredentials->getIdentifier(),
            $content['oauth_verifier']
        );

        $content['access_token'] = $tokenCredentials->getIdentifier();
        $content['access_token_secret'] = $tokenCredentials->getSecret();

        $this->write($content);
    }


    public static function bind(): void
    {
        app()->singleton(RetrievesAccessToken::class, static::class);
    }
}
