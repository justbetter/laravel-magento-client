<?php

namespace JustBetter\MagentoClient\Client;

use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use JustBetter\MagentoClient\Contracts\BuildsRequest;
use JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore;

class Magento
{
    public string $connection;

    public ?string $storeCode = null;

    public function __construct(
        protected BuildsRequest $request
    ) {
        $this->connection = config('magento.connection');
    }

    public function connection(string $connection): static
    {
        $this->connection = $connection;

        return $this;
    }

    public function store(?string $store = null): static
    {
        $this->storeCode = $store;

        return $this;
    }

    public function graphql(string $query, array $variables = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)
            ->when($this->storeCode !== null, fn(PendingRequest $request) => $request->withHeader('Store', $this->storeCode))
            ->post('/graphql', [
                'query' => $query,
                'variables' => $variables
            ]);

        return $response;
    }

    public function get(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->get($this->getUrl($path), $data);

        return $response;
    }

    public function post(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->post($this->getUrl($path), $data);

        return $response;
    }

    public function postAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->post($this->getUrl($path, true), $data);

        return $response;
    }

    public function postBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->post($this->getUrl($path, true, true), $data);

        return $response;
    }

    public function patch(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->patch($this->getUrl($path), $data);

        return $response;
    }

    public function patchAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->patch($this->getUrl($path, true), $data);

        return $response;
    }

    public function patchBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->patch($this->getUrl($path, true, true), $data);

        return $response;
    }

    public function put(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->put($this->getUrl($path), $data);

        return $response;
    }

    public function putAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->put($this->getUrl($path, true), $data);

        return $response;
    }

    public function putBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->put($this->getUrl($path, true, true), $data);

        return $response;
    }

    public function delete(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->delete($this->getUrl($path), $data);

        return $response;
    }

    public function deleteAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->delete($this->getUrl($path, true), $data);

        return $response;
    }

    public function deleteBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build($this->connection)->delete($this->getUrl($path, true, true), $data);

        return $response;
    }

    /** @return LazyCollection<int, array> */
    public function lazy(string $path, array $data = [], int $pageSize = 100): LazyCollection
    {
        return LazyCollection::make(function () use ($path, $data, $pageSize): Generator {
            $currentPage = 1;
            $hasNextPage = true;

            while ($hasNextPage) {
                $data['searchCriteria[pageSize]'] = $pageSize;
                $data['searchCriteria[currentPage]'] = $currentPage;

                $response = $this->get($path, $data)->throw();

                /** @var Enumerable<int, array<string, mixed>> $items */
                $items = $response->collect('items');

                foreach ($items as $item) {
                    yield $item;
                }

                $hasNextPage = $items->count() >= $pageSize;
                $currentPage++;
            }
        });
    }

    public function getUrl(string $path, bool $async = false, bool $bulk = false): string
    {
        /** @var array $config */
        $config = config('magento.connections.'.$this->connection);

        $options = [
            $config['base_path'] ?? 'rest',
            $this->storeCode ?? $config['store_code'] ?? 'all',
        ];

        if ($async || $bulk) {
            $options[] = 'async';

            if ($bulk) {
                $options[] = 'bulk';
            }
        }

        $options[] = $config['version'] ?? 'V1';
        $options[] = $path;

        return implode('/', $options);
    }

    public static function fake(): void
    {
        config()->set('magento.connection', 'default');
        config()->set('magento.connections.default', [
            'base_url' => 'magento',
            'base_path' => 'rest',
            'store_code' => 'all',
            'version' => 'V1',
            'access_token' => '::token::',
            'timeout' => 30,
            'connect_timeout' => 10,
            'authentication_method' => 'token',
        ]);

        config()->set('magento.oauth', [
            'middleware' => [],
            'prefix' => 'magento/oauth',
            'keystore' => FileKeyStore::class,
        ]);
    }
}
