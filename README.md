# Laravel Magento Client

<p>
    <a href="https://github.com/justbetter/laravel-magento-client"><img src="https://img.shields.io/github/actions/workflow/status/justbetter/laravel-magento-client/tests.yml?label=tests&style=flat-square" alt="Tests"></a>
    <a href="https://github.com/justbetter/laravel-magento-client"><img src="https://img.shields.io/github/actions/workflow/status/justbetter/laravel-magento-client/coverage.yml?label=coverage&style=flat-square" alt="Coverage"></a>
    <a href="https://github.com/justbetter/laravel-magento-client"><img src="https://img.shields.io/github/actions/workflow/status/justbetter/laravel-magento-client/analyse.yml?label=analysis&style=flat-square" alt="Analysis"></a>
    <a href="https://github.com/justbetter/laravel-magento-client"><img src="https://img.shields.io/packagist/dt/justbetter/laravel-magento-client?color=blue&style=flat-square" alt="Total downloads"></a>
</p>

A client to communicate with Magento from your Laravel application.

```php
<?php

class Example
{
    public function __construct(
        protected \JustBetter\MagentoClient\Client\Magento $magento,
    ) {
    }

    public function retrieveProduct()
    {
        $response = $this->magento->get('products/1');
    }

    public function retrieveOrdersLazily()
    {
        $retrievedOrders = [];

        $searchCriteria = \JustBetter\MagentoClient\Query\SearchCriteria::make()
            ->where('state', 'processing');

        foreach ($this->magento->lazy('orders', $searchCriteria->get()) as $order) {
            $retrievedOrders[] = $order['increment_id'];
        }
    }
}

```

> Looking to synchronize [prices](https://github.com/justbetter/laravel-magento-prices)
> or [stock](https://github.com/justbetter/laravel-magento-stock) to Magento?

## Installation and Configuration

> Are you coming from `grayloon/laravel-magento-api`? We have written
> a [migration guide](./docs/migrations/GRAYLOON.md)!

Require this package:

```shell
composer require justbetter/laravel-magento-client
```

Add the following to your `.env`:

```.dotenv
MAGENTO_BASE_URL=
MAGENTO_ACCESS_TOKEN=
```

## Authentication

By default, this packages uses Bearer tokens to authenticate to Magento. Since Magento 2.4.4, this method of authentication requires additional configuration in Magento as [described here](https://developer.adobe.com/commerce/webapi/get-started/authentication/gs-authentication-token).

It is recommended to authenticate to Magento using OAuth 1.0 which will also prevent the additional configuration requirements, besides setting up the integration itself.

### Setting up OAuth 1.0

Start by adding the following variable to your `.env`-file.

```.dotenv
MAGENTO_AUTH_METHOD=oauth
```

Note that you can also remove `MAGENTO_ACCESS_TOKEN` at this point, as it will be saved in a file instead.

Next, open Magento and create a new integration. When configuring, supply a `Callback URL` and `Identity link URL`. If you have not made any changes to your configuration, these are the URLs:

```
Callback URL:      https://example.com/magento/oauth/callback
Identity link URL: https://example.com/magento/oauth/identity
```

When creating the integration, Magento will send multiple tokens and secrets to your application via the `callback`-endpoint. This information will be saved in a JSON file, as configured in `magento.php`. Magento will redirect you to the `identity`-endpoint in order to activate the integration.

For more information about OAuth 1.0 in Magento, please consult the [documentation](https://developer.adobe.com/commerce/webapi/get-started/authentication/gs-authentication-oauth).

#### Identity endpoint

Note that the `identity`-endpoint **does not** have any authentication or authorization middleware by default - you should add this in the configuration yourself. If you do not have any form of protection, anyone could change the tokens in your secret file.

It is recommended that only administrators of your application are allowed to access the identity endpoint.

## Usage

You can get an instance of the client by injecting it or by resolving it:

```php
<?php

public function __construct(
    protected \JustBetter\MagentoClient\Client\Magento $magento
) {

}

// OR

/** @var \JustBetter\MagentoClient\Client\Magento $client */
$client = app(\JustBetter\MagentoClient\Client\Magento::class);
```

After you got an instance you can use the `get`, `post`, `put` and `patch` methods to use the Magento API.

### SearchCriteria / Filtering

To easily create search criteria you can use the `\JustBetter\MagentoClient\Query\SearchCriteria` class.
For example:

```php
<?php

$search = \JustBetter\MagentoClient\Query\SearchCriteria::make()
        ->where('sku', '!=', '123')
        ->orWhere('price', '>', 10),
        ->whereIn('sku', ['123', '456'])
        ->orWhereNull('name')
        ->paginate(1, 50)
        ->get();
```

You can view more examples in [Magento's documentation](https://developer.adobe.com/commerce/webapi/rest/use-rest/performing-searches/).

### Pre defined requests (deprecated)

> Requests are deprecated as the `lazy` functionality has been made available in the client itself.

This package comes bundled with a few predefined request so that you do not have to reinvent the wheel.

### More Examples

You can view the tests for more examples.

## Testing

This package uses Laravel's HTTP client so that you can fake the requests.

```php
<?php

Http::fake([
    '*/rest/all/V1/products*' => Http::response([
        'items' => [['sku' => '::some_sku::']],
        'total_count' => 1,
    ]),
]);
```

## Quality

To ensure the quality of this package, run the following command:

```shell
composer quality
```

This will execute three tasks:

1. Makes sure all tests are passed
2. Checks for any issues using static code analysis
3. Checks if the code is correctly formatted

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Vincent Boon](https://github.com/VincentBean)
- [Ramon Rietdijk](https://github.com/ramonrietdijk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
