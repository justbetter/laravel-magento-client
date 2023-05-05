<?php

namespace JustBetter\MagentoClient\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JustBetter\MagentoClient\Enums\AuthenticationMethod;
use Symfony\Component\HttpFoundation\Response;

/**
 * This middleware will prevent any OAuth routes from being accessable when the authentication method is not set to "oauth"
 */
class OAuthMiddleware
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var string $method */
        $method = config('magento.authentication_method');

        $authMethod = AuthenticationMethod::from($method);

        if ($authMethod !== AuthenticationMethod::OAuth) {
            abort(403);
        }

        return $next($request);
    }
}
