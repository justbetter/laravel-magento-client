<?php

namespace JustBetter\MagentoClient\Actions;

use JustBetter\MagentoClient\Contracts\RetrievesBearerToken;

class RetrieveIntegrationToken implements RetrievesBearerToken
{
    public function retrieve(): string
    {
        return config('magento.access_token');
    }

    public static function bind(): void
    {
        app()->singleton(RetrievesBearerToken::class, static::class);
    }
}
