<?php

namespace JustBetter\MagentoClient\OAuth\KeyStore;

use JustBetter\MagentoClient\Models\OAuthKey;

class DatabaseKeyStore extends KeyStore
{
    public function get(string $connection): array
    {
        /** @var ?OAuthKey $key */
        $key = OAuthKey::query()->firstWhere('magento_connection', '=', $connection);

        return $key?->keys ?? [];
    }

    public function set(string $connection, array $data): void
    {
        OAuthKey::query()->updateOrCreate(
            [
                'magento_connection' => $connection,
            ],
            [
                'keys' => $data,
            ]
        );
    }
}
