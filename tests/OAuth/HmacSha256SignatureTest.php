<?php

namespace JustBetter\MagentoClient\Tests\Providers;

use JustBetter\MagentoClient\OAuth\HmacSha256Signature;
use JustBetter\MagentoClient\Tests\TestCase;
use League\OAuth1\Client\Credentials\ClientCredentials;

class HmacSha256SignatureTest extends TestCase
{
    /** @test */
    public function it_can_create_signatures(): void
    {
        $clientCredentials = new ClientCredentials;
        $clientCredentials->setIdentifier('::oauth-consumer-key::');
        $clientCredentials->setSecret('::oauth-consumer-secret::');

        /** @var HmacSha256Signature $signature */
        $signature = app()->makeWith(HmacSha256Signature::class, [
            'clientCredentials' => $clientCredentials,
        ]);

        $method = $signature->method();

        $this->assertEquals('HMAC-SHA256', $method);

        $signed = $signature->sign('test');

        $this->assertEquals('mpy8OxeIYvroN5WQW9f/aBMtC+qbmus1oaa0KAMoMiQ=', $signed);
    }
}
