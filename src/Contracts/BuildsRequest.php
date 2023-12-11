<?php

namespace JustBetter\MagentoClient\Contracts;

use Illuminate\Http\Client\PendingRequest;

interface BuildsRequest
{
    /** Build a basic pending request to Magento */
    public function build(string $connection): PendingRequest;
}
