<?php

return [

    'connection' => 'default',

    'connections' => [
        'default' => [
            /* Base URL of Magento, for example: https://magento.test */
            'base_url' => env('MAGENTO_BASE_URL'),

            /* Base path, only modify if your API is not at /rest */
            'base_path' => env('MAGENTO_BASE_PATH', 'rest'),

            /* Graphql path, only modify if your API is not at /graphql */
            'graphql_path' => env('MAGENTO_GRAPHQL_PATH', 'graphql'),

            /* Store code, modify if you want to set a store by default. */
            'store_code' => env('MAGENTO_STORE_CODE', 'all'),

            /* Modify if Magento has a new API version */
            'version' => env('MAGENTO_API_VERSION', 'V1'),

            /* Magento access token of an integration */
            'access_token' => env('MAGENTO_ACCESS_TOKEN'),

            /* Specify the timeout (in seconds) for the request. */
            'timeout' => 30,

            /* Specify the connection timeout (in seconds) for the request. */
            'connect_timeout' => 10,

            /* Authentication method, choose either "oauth" or "token". */
            'authentication_method' => env('MAGENTO_AUTH_METHOD', 'token'),

            /* Availability configuration. */
            'availability' => [
                /* The response codes that should trigger the availability check. */
                'codes' => [502, 503, 504],

                /* The amount of failed requests before the service is marked as unavailable. */
                'threshold' => 10,

                /* The timespan in minutes in which the failed requests should occur. */
                'timespan' => 10,

                /* The cooldown in minutes after the threshold is reached. */
                'cooldown' => 2,
            ],
        ],
    ],

    /* OAuth configuration */
    'oauth' => [

        /* Add your middleware that authenticates users here, this is used for the identity callback. */
        'middleware' => [
            //
        ],

        /* Prefix for the oauth routes. */
        'prefix' => 'magento/oauth',

        /* Class that manages how the keys are stored */
        'keystore' => \JustBetter\MagentoClient\OAuth\KeyStore\DatabaseKeyStore::class,
    ],
];
