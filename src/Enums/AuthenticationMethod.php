<?php

namespace JustBetter\MagentoClient\Enums;

use JustBetter\MagentoClient\Providers\BaseProvider;
use JustBetter\MagentoClient\Providers\BearerTokenProvider;
use JustBetter\MagentoClient\Providers\OAuthProvider;

enum AuthenticationMethod: string
{
    case Token = 'token';
    case OAuth = 'oauth';

    public function provider(): BaseProvider
    {
        $class = match ($this) {
            AuthenticationMethod::Token => BearerTokenProvider::class,
            AuthenticationMethod::OAuth => OAuthProvider::class,
        };

        /** @var BaseProvider $instance */
        $instance = app($class);

        return $instance;
    }
}
