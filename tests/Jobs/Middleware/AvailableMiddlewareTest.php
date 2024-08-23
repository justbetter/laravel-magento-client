<?php

namespace JustBetter\MagentoClient\Tests\Jobs\Middleware;

use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Jobs\Middleware\AvailableMiddleware;
use JustBetter\MagentoClient\Tests\Fakes\TestJob;
use JustBetter\MagentoClient\Tests\TestCase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class AvailableMiddlewareTest extends TestCase
{
    #[Test]
    public function it_can_dispatch_jobs(): void
    {
        $middleware = new AvailableMiddleware('default');

        $job = new TestJob;
        $ran = false;

        $middleware->handle($job, function () use (&$ran): void {
            $ran = true;
        });

        $this->assertTrue($ran);
    }

    #[Test]
    public function it_can_release_jobs(): void
    {
        $this->mock(Magento::class, function (MockInterface $mock): void {
            $mock->shouldReceive('connection')->with('default')->once()->andReturnSelf();
            $mock->shouldReceive('available')->once()->andReturnFalse();
        });

        $job = $this->mock(TestJob::class, function (MockInterface $mock): void {
            $mock->shouldReceive('release')->once();
        });

        $middleware = new AvailableMiddleware('default');
        $middleware->handle($job, function (): void {
            $this->fail('Job should not have run.');
        });
    }
}
