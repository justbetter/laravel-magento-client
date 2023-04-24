<?php

namespace JustBetter\MagentoClient\OAuth;

use League\OAuth1\Client\Credentials\CredentialsException;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Credentials\TokenCredentials;
use League\OAuth1\Client\Server\Server;

class MagentoServer extends Server
{
    public string $verifier;

    public function urlTemporaryCredentials(): string
    {
        return config('magento.base_url').'/oauth/token/request';
    }

    public function urlAuthorization()
    {
        //
    }

    public function urlTokenCredentials(): string
    {
        return config('magento.base_url').'/oauth/token/access';
    }

    public function getTokenCredentials(TemporaryCredentials $temporaryCredentials, $temporaryIdentifier, $verifier)
    {
        $this->verifier = $verifier;

        return parent::getTokenCredentials($temporaryCredentials, $temporaryIdentifier, $verifier);
    }

    protected function additionalProtocolParameters(): array
    {
        return [
            'oauth_verifier' => $this->verifier,
        ];
    }

    protected function createTemporaryCredentials($body): TemporaryCredentials
    {
        parse_str($body, $data);

        if ( ! $data || ! is_array($data)) {
            throw new CredentialsException('Unable to parse temporary credentials response.');
        }

        $temporaryCredentials = new TemporaryCredentials();
        $temporaryCredentials->setIdentifier($data['oauth_token']);
        $temporaryCredentials->setSecret($data['oauth_token_secret']);

        return $temporaryCredentials;
    }

    public function urlUserDetails()
    {
        //
    }

    public function userDetails($data, TokenCredentials $tokenCredentials)
    {
        //
    }

    public function userUid($data, TokenCredentials $tokenCredentials)
    {
        //
    }

    public function userEmail($data, TokenCredentials $tokenCredentials)
    {
        //
    }

    public function userScreenName($data, TokenCredentials $tokenCredentials)
    {
        //
    }
}
