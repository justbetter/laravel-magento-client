<?php

namespace JustBetter\MagentoClient\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;
use JustBetter\MagentoClient\Models\MagentoOAuth;

trait HasMagentoOAuth
{
    public function magentoOAuth(): HasOne
    {
        return $this->hasOne(MagentoOAuth::class, 'owner_id');
    }
}