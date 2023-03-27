<?php

namespace JustBetter\MagentoClient\Enums;

enum AuthenticationMethod: string
{
    case Token = 'token';
    case OAuth = 'oauth';
}
