<?php

namespace JustBetter\MagentoClient\Tests\Listeners;

use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Actions\CheckMagento;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Listeners\StoreAvailabilityListener;
use JustBetter\MagentoClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class StoreAvailabilityListenerTest extends TestCase
{
    #[Test]
    public function it_can_be_available(): void
    {
        Http::fake([
            '*' => Http::response(null, 200),
        ])->preventStrayRequests();

        $magento = app(Magento::class);
        $magento->get('/');

        $this->assertNull(
            cache()->get(StoreAvailabilityListener::COUNT_KEY.'default')
        );
    }

    #[Test]
    public function it_can_keep_track_of_the_count(): void
    {
        Http::fake([
            '*' => Http::response(null, 503),
        ])->preventStrayRequests();

        /** @var Magento $magento */
        $magento = app(Magento::class);
        $magento->get('/');

        $this->assertEquals(1, cache()->get(StoreAvailabilityListener::COUNT_KEY.'default'));
        $this->assertNull(cache()->get(CheckMagento::AVAILABLE_KEY.'default'));
    }

    #[Test]
    public function it_can_be_unavailable(): void
    {
        Http::fake([
            '*' => Http::response(null, 503),
        ])->preventStrayRequests();

        config()->set('magento.connections.default.availability.threshold', 1);

        /** @var Magento $magento */
        $magento = app(Magento::class);
        $magento->get('/');

        $this->assertNull(cache()->get(StoreAvailabilityListener::COUNT_KEY.'default'));
        $this->assertFalse(cache()->get(CheckMagento::AVAILABLE_KEY.'default'));
    }
}
