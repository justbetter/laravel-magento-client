<?php

namespace JustBetter\MagentoClient\Actions;

use JustBetter\MagentoClient\Contracts\ChecksMagento;

class CheckMagento implements ChecksMagento
{
    public const AVAILABLE_KEY = 'magento-client:available:';

    public function available(string $connection): bool
    {
        return cache()->get(static::AVAILABLE_KEY.$connection, true);
    }

    public static function bind(): void
    {
        app()->singleton(ChecksMagento::class, static::class);
    }
}
