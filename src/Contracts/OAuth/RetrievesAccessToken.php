<?php

namespace JustBetter\MagentoClient\Contracts\OAuth;

interface RetrievesAccessToken
{
    public function retrieve(): void;
}
