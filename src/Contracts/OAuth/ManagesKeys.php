<?php

namespace JustBetter\MagentoClient\Contracts\OAuth;

interface ManagesKeys
{
    public function get(): array;

    public function set(array $data): void;

    public function merge(array $data): void;
}
