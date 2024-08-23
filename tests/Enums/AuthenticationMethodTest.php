<?php

namespace JustBetter\MagentoClient\Tests\Enums;

use JustBetter\MagentoClient\Enums\AuthenticationMethod;
use JustBetter\MagentoClient\Providers\BearerTokenProvider;
use JustBetter\MagentoClient\Providers\OAuthProvider;
use JustBetter\MagentoClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class AuthenticationMethodTest extends TestCase
{
    #[Test]
    #[DataProvider('providers')]
    public function it_can_get_the_provider(AuthenticationMethod $authenticationMethod, string $expectedProvider): void
    {
        $this->assertInstanceOf($expectedProvider, $authenticationMethod->provider());
    }

    public static function providers(): array
    {
        return [
            [
                AuthenticationMethod::Token,
                BearerTokenProvider::class,
            ],
            [
                AuthenticationMethod::OAuth,
                OAuthProvider::class,
            ],
        ];
    }
}
