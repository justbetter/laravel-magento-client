<?php

declare(strict_types=1);

namespace JustBetter\MagentoClient\OAuth;

use League\OAuth1\Client\Signature\HmacSha1Signature;

class HmacSha256Signature extends HmacSha1Signature
{
    #[\Override]
    public function method(): string
    {
        return 'HMAC-SHA256';
    }

    #[\Override]
    protected function hash($string): string
    {
        return hash_hmac('sha256', $string, $this->key(), true);
    }
}
