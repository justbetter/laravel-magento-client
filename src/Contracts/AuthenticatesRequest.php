<?php

namespace JustBetter\MagentoClient\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface AuthenticatesRequest
{
    public function authenticate(PendingRequest $request): PendingRequest;
}
