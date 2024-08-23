<?php

namespace JustBetter\MagentoClient\Listeners;

use JustBetter\MagentoClient\Actions\CheckMagento;
use JustBetter\MagentoClient\Events\MagentoResponseEvent;

class StoreAvailabilityListener
{
    public const COUNT_KEY = 'magento-client:response:count:unavailable:';

    public function handle(MagentoResponseEvent $event): void
    {
        /** @var array<int, int> $codes */
        $codes = config('magento.connections.'.$event->connection.'.availability.codes', [502, 503, 504]);

        if (! in_array($event->response->status(), $codes)) {
            return;
        }

        $countKey = static::COUNT_KEY.$event->connection;

        /** @var int $count */
        $count = cache()->get($countKey, 0);
        $count++;

        /** @var int $threshold */
        $threshold = config('magento.connections.'.$event->connection.'.availability.threshold', 10);

        /** @var int $timespan */
        $timespan = config('magento.connections.'.$event->connection.'.availability.timespan', 10);

        /** @var int $cooldown */
        $cooldown = config('magento.connections.'.$event->connection.'.availability.cooldown', 2);

        cache()->put($countKey, $count, now()->addMinutes($timespan));

        if ($count >= $threshold) {
            cache()->put(CheckMagento::AVAILABLE_KEY.$event->connection, false, now()->addMinutes($cooldown));

            cache()->forget($countKey);
        }
    }
}
