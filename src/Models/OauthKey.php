<?php

namespace JustBetter\MagentoClient\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $magento_connection
 * @property array $keys
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class OauthKey extends Model
{
    protected $table = 'magento_oauth_keys';

    protected $guarded = [];

    protected $casts = [
        'keys' => 'array',
    ];
}
