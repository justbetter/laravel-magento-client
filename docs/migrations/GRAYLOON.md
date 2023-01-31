# Migration guide from grayloon/laravel-magento-api

This is a migration guide to migrate from `grayloon/laravel-magento-api` to `justbetter/laravel-magento-client`.

## Installation

> It is possible to use both clients in a project without issues.

First of all, remove the `grayloon/laravel-magento-api` package.

```shell
composer remove grayloon/laravel-magento-api
```

Next, install the `justbetter/laravel-magento-client` package.

```shell
composer require justbetter/laravel-magento-client
```

## Configuration

If you have not modified the configuration file there are no changes necessary. The variables defined in the `.env`-file
are equal, thus backwards compatible.

## Logging Failed Requests

The `grayloon/laravel-magento-api` package contains a feature that logs failed API requests to the Laravel log file when
the option `log_failed_requests` is enabled in the configuration file.

This package does not have this functionality. We aim to keep this package simplistic and expect you to handle errors
yourself.

## Update Your Code

All references to Grayloon have to be updated to use the new client.

```php
<?php

- use Grayloon\Magento\Magento;
+ use JustBetter\MagentoClient\Client\Magento;
```

The new client does not have API-classes. Calls to endpoints have to be made via de Magento client directly or by using
one of our predefined request classes. See the [README.md](../../README.md) for more information, there you will also
see an example of loading results lazily.

Our client is built to be used via dependency injection and does not have a facade.

### Requests

A few examples will be listed below.

```php
<?php

use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Query\SearchCriteria;

class Example
{
    public function __construct(
        protected Magento $magento
    ) {
    }

    public function getProduct(string $sku): void
    {
        // Before
        Magento::api('products')->show($sku);

        // After
        $this->magento->get("products/$sku");
    }

    public function updateProduct(string $sku, array $data): void
    {
        // Before
        Magento::api('products')->edit($sku, $data);

        // After
        $this->magento->put("products/$sku", $data);
    }

    public function getSpecificCustomers(): void
    {
        // Before
        Magento::api('customers')->all(50, 1, [
            'searchCriteria[filter_groups][0][filters][0][field]' => 'email',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'like',
            'searchCriteria[filter_groups][0][filters][0][value]' => '%@example.com',
        ]);

        // After
        $searchCriteria = SearchCriteria::make()
            ->paginate(1, 50)
            ->where('email', 'like', '%@example.com')
            ->get();

        $this->magento->get('customers', $searchCriteria);
    }

    public function getStoreConfigs(): void
    {
        // Before
        Magento::api('store')->storeConfigs();

        // After
        $this->magento->get('store/storeConfigs');
    }

    public function getWebsites(): void
    {
        // Before
        Magento::api('store')->websites();

        // After
        $this->magento->get('store/websites');
    }
}
```
