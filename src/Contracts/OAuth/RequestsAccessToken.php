<?php

namespace JustBetter\MagentoClient\Contracts\OAuth;

interface RequestsAccessToken
{
    public function request(string $connection, string $key): void;
}
