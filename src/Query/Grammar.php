<?php

namespace JustBetter\MagentoClient\Query;

use JustBetter\MagentoClient\Exceptions\InvalidOperatorException;

class Grammar
{
    public array $operatorMapping = [
        '=' => 'eq',
        '<' => 'lt',
        '>' => 'gt',
        '<=' => 'lteq',
        '>=' => 'gteq',
        '<>' => 'neq',
        '!=' => 'neq',
        'in' => 'in',
        'nin' => 'nin',
    ];

    public function getOperator(string $operator): string
    {
        return $this->operatorMapping[$operator];
    }

    public function checkOperator(string $operator): void
    {
        $validOperators = array_keys($this->operatorMapping);

        if (in_array($operator, $validOperators)) {
            return;
        }

        throw new InvalidOperatorException($operator, $validOperators);
    }
}
