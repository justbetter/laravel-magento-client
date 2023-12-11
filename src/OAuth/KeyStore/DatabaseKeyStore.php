<?php

namespace JustBetter\MagentoClient\OAuth\KeyStore;

use JustBetter\MagentoClient\Models\OauthKey;

class DatabaseKeyStore extends KeyStore
{
    public function get(string $connection): array
    {
        /** @var ?OauthKey $key */
        $key = OauthKey::query()->firstWhere('magento_connection', '=', $connection);

        return $key?->keys ?? [];
    }

    public function set(string $connection, array $data): void
    {
        OauthKey::query()->updateOrCreate(
            [
                'magento_connection' => $connection,
            ],
            [
                'keys' => $data,
            ]
        );
    }
}
