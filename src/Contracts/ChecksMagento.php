<?php

declare(strict_types=1);

namespace JustBetter\MagentoClient\Contracts;

interface ChecksMagento
{
    public function available(string $connection): bool;
}
