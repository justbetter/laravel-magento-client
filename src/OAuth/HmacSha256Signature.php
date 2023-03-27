<?php

namespace JustBetter\MagentoClient\OAuth;

use League\OAuth1\Client\Signature\HmacSha1Signature;

class HmacSha256Signature extends HmacSha1Signature
{
    public function method(): string
    {
        return 'HMAC-SHA256';
    }

    protected function hash($string): string
    {
        return hash_hmac('sha256', $string, $this->key(), true);
    }
}
