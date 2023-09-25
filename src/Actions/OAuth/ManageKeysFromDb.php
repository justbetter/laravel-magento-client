<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use JustBetter\MagentoClient\Contracts\OAuth\ManagesKeys;
use JustBetter\MagentoClient\Models\MagentoOAuth;

class ManageKeysFromDb implements ManagesKeys
{
    private ?string $oauth_consumer_key = null;

    public function __construct()
    {
        $this->oauth_consumer_key = request()->input('oauth_consumer_key');
    }

    public function get(): array
    {
        if (! $keys = MagentoOAuth::whereOauthConsumerKey($this->oauth_consumer_key)->first()) {
            return [];
        }

        return $keys->toArray();
    }

    public function set(array $data): void
    {
        MagentoOAuth::updateOrCreate(
            ['oauth_consumer_key' => $this->oauth_consumer_key],
            $data
        );
    }

    public function merge(array $data): void
    {
        $merged = array_merge($this->get(), $data);

        $this->set($merged);
    }

    public static function bind(): void
    {
        app()->singleton(ManagesKeys::class, static::class);
    }
}
