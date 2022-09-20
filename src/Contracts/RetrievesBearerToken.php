<?php

namespace JustBetter\MagentoClient\Contracts;

interface RetrievesBearerToken
{
    public function retrieve(): string;
}
