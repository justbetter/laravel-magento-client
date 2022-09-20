<?php

namespace JustBetter\MagentoClient\Exceptions;

use Exception;

class InvalidOperatorException extends Exception
{
    public function __construct(string $operator, array $validOperators = [])
    {
        $message = "Operator $operator is not valid, use one of ".implode(', ', $validOperators);

        parent::__construct($message);
    }
}
