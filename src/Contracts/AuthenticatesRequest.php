<?php

namespace JustBetter\MagentoClient\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface AuthenticatesRequest
{
    public function authenticate(string $connection, PendingRequest $request): PendingRequest;
}
