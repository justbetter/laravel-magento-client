<?php

namespace JustBetter\MagentoClient\Contracts;

interface ChecksMagento
{
    public function available(string $connection): bool;
}
