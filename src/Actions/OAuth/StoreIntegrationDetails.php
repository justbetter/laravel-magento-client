<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use JustBetter\MagentoClient\Concerns\InteractsWithOAuthSecretFile;
use JustBetter\MagentoClient\Contracts\OAuth\StoresIntegrationDetails;

class StoreIntegrationDetails implements StoresIntegrationDetails
{
    use InteractsWithOAuthSecretFile;

    public function store(array $data): void
    {
        $content = $this->read();

        if ($content['oauth_consumer_key'] !== $data['oauth_consumer_key']) {
            abort(403);
        }

        $this->write($data);
    }

    public static function bind(): void
    {
        app()->singleton(StoresIntegrationDetails::class, static::class);
    }
}
