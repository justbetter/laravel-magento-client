<?php

namespace JustBetter\MagentoClient\Providers;

use Illuminate\Http\Client\PendingRequest;

abstract class BaseProvider
{
    abstract public function authenticate(PendingRequest $request, string $connection): PendingRequest;
}
