<?php

namespace JustBetter\MagentoClient\OAuth\KeyStore;

abstract class KeyStore
{
    public static function instance(): KeyStore
    {
        /** @var class-string<KeyStore> $class */
        $class = config('magento.oauth.keystore');

        /** @var KeyStore $instance */
        $instance = app($class);

        return $instance;
    }

    abstract public function get(string $connection): array;

    abstract public function set(string $connection, array $data): void;

    public function merge(string $connection, array $data): void
    {
        $merged = array_merge($this->get($connection), $data);

        $this->set($connection, $merged);
    }
}
