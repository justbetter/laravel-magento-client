<?php

namespace JustBetter\MagentoClient\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JustBetter\MagentoClient\Models\MagentoOAuth;

class MagentoOAuthFactory extends Factory
{
    protected $model = MagentoOAuth::class;

    public function definition()
    {
        return [
            'oauth_consumer_key' => $this->faker->regexify('[a-z0-9]{32}'),
            'oauth_consumer_secret' => $this->faker->regexify('[a-z0-9]{32}'),
            'oauth_verifier' => $this->faker->regexify('[a-z0-9]{32}'),
            'access_token' => $this->faker->regexify('[a-z0-9]{32}'),
            'access_token_secret' => $this->faker->regexify('[a-z0-9]{32}'),
            'callback' => [
                'oauth_verifier' => $this->faker->regexify('[a-z0-9]{32}'),
                'oauth_consumer_key' => $this->faker->regexify('[a-z0-9]{32}'),
                'oauth_consumer_secret' => $this->faker->regexify('[a-z0-9]{32}'),
            ],
            'user_id' => null,
        ];
    }
}
