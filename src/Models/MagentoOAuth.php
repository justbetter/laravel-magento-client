<?php

namespace JustBetter\MagentoClient\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User;
use JustBetter\MagentoClient\Database\Factories\MagentoOAuthFactory;

class MagentoOAuth extends Model
{
    use HasFactory;

    protected $table = 'magento_oauth';

    protected $guarded = [];

    protected $casts = [
        'callback' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('magento.oauth.keys.owner'));
    }

    public static function associateConsumerKeyToOwner(string $oauth_consumer_key, Model $model): bool
    {
        if ($keys = self::whereOauthConsumerKey($oauth_consumer_key)->first()) {
            $keys->owner()->associate($model);
            $keys->save();
            return true;
        }

        return false;
    }

    protected static function newFactory(): Factory
    {
        return MagentoOAuthFactory::new();
    }
}