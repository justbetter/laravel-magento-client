<?php

namespace JustBetter\MagentoClient\Tests\Actions;

use JustBetter\MagentoClient\Actions\CheckMagento;
use JustBetter\MagentoClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CheckMagentoTest extends TestCase
{
    #[Test]
    public function it_can_be_available(): void
    {
        /** @var CheckMagento $action */
        $action = app(CheckMagento::class);

        $this->assertTrue($action->available('default'));
    }

    #[Test]
    public function it_can_be_unavailable(): void
    {
        /** @var CheckMagento $action */
        $action = app(CheckMagento::class);

        cache()->put(CheckMagento::AVAILABLE_KEY.config('magento.connection'), false);

        $this->assertFalse($action->available('default'));
    }

    #[Test]
    public function it_can_handle_multiple_connections(): void
    {
        /** @var CheckMagento $action */
        $action = app(CheckMagento::class);

        cache()->put(CheckMagento::AVAILABLE_KEY.'connection', false);
        cache()->put(CheckMagento::AVAILABLE_KEY.'another-connection', true);

        $this->assertFalse($action->available('connection'));
        $this->assertTrue($action->available('another-connection'));
    }
}
