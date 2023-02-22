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

This package uses an integration token, since 2.4.4 the default is an OAuth token. This is not implemented yet (feel
free to make a PR).
See [this page](https://developer.adobe.com/commerce/webapi/get-started/authentication/gs-authentication-token) for more
information.

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
        ->paginate(1, 50)
        ->get();
```

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
