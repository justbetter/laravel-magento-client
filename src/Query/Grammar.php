<?php

namespace JustBetter\MagentoClient\Query;

class Grammar
{
    public array $mapping = [
        '=' => 'eq',
        '<' => 'lt',
        '>' => 'gt',
        '<=' => 'lteq',
        '>=' => 'gteq',
        '<>' => 'neq',
        '!=' => 'neq',
    ];

    public function getOperator(string $operator): string
    {
        return $this->mapping[$operator] ?? $operator;
    }
}
