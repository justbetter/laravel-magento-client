<?php

namespace JustBetter\MagentoClient\Contracts\OAuth;

interface StoresIntegrationDetails
{
    public function store(array $data): void;
}
