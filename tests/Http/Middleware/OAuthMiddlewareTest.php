<?php

namespace JustBetter\MagentoClient\Tests\Http\Middleware;

use Illuminate\Http\Response;
use JustBetter\MagentoClient\Http\Middleware\OAuthMiddleware;
use JustBetter\MagentoClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OAuthMiddlewareTest extends TestCase
{
    #[Test]
    public function it_can_pass(): void
    {
        config()->set('magento.connections.default.authentication_method', 'oauth');

        /** @var OAuthMiddleware $middleware */
        $middleware = app(OAuthMiddleware::class);

        /** @var Response $response */
        $response = $middleware->handle(request(), function (): Response {
            return response('passed');
        });

        $this->assertEquals('passed', $response->getContent());
    }

    #[Test]
    public function it_can_abort(): void
    {
        $this->expectException(HttpException::class);

        /** @var OAuthMiddleware $middleware */
        $middleware = app(OAuthMiddleware::class);
        $middleware->handle(request(), function (): Response {
            return response('passed');
        });
    }
}
