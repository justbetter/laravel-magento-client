<?php

namespace JustBetter\MagentoClient\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JustBetter\MagentoClient\Enums\AuthenticationMethod;
use Symfony\Component\HttpFoundation\Response;

/**
 * This middleware will prevent any OAuth routes from being accessible when OAuth is not active on any of the connections
 */
class OAuthMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var array $connections */
        $connections = config('magento.connections');

        foreach ($connections as $connection) {

            /** @var string $method */
            $method = $connection['authentication_method'];
            if (AuthenticationMethod::from($method) === AuthenticationMethod::OAuth) {
                return $next($request);
            }

        }

        abort(403);
    }
}
