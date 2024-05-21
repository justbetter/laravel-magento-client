<?php

namespace JustBetter\MagentoClient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Client\Response;

class MagentoResponseEvent
{
    use Dispatchable;

    public function __construct(
        public Response $response
    ) {

    }
}
