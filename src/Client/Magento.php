<?php

namespace JustBetter\MagentoClient\Client;

use Closure;
use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use JustBetter\MagentoClient\Contracts\BuildsRequest;
use JustBetter\MagentoClient\Contracts\ChecksMagento;
use JustBetter\MagentoClient\Events\MagentoResponseEvent;
use JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore;

class Magento
{
    public string $connection;

    public ?string $storeCode = null;

    public ?Closure $interceptor;

    public function __construct(
        protected BuildsRequest $request,
        protected ChecksMagento $checksMagento,
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
        /** @var string $endpoint */
        $endpoint = config("magento.connections.{$this->connection}.graphql_path");

        /** @var Response $response */
        $response = $this->request()
            ->when($this->storeCode !== null, fn (PendingRequest $request): PendingRequest => $request->withHeaders(['Store' => $this->storeCode]))
            ->post($endpoint, [
                'query' => $query,
                'variables' => $variables,
            ]);

        return $this->handleResponse($response);
    }

    public function get(string $path, array $data = []): Response
    {
        $response = $this->request()->get($this->getUrl($path), $data);

        return $this->handleResponse($response);
    }

    public function post(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->post($this->getUrl($path), $data);

        return $this->handleResponse($response);
    }

    public function postAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->post($this->getUrl($path, true), $data);

        return $this->handleResponse($response);
    }

    public function postBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->post($this->getUrl($path, true, true), $data);

        return $this->handleResponse($response);
    }

    public function patch(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->patch($this->getUrl($path), $data);

        return $this->handleResponse($response);
    }

    public function patchAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->patch($this->getUrl($path, true), $data);

        return $this->handleResponse($response);
    }

    public function patchBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->patch($this->getUrl($path, true, true), $data);

        return $this->handleResponse($response);
    }

    public function put(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->put($this->getUrl($path), $data);

        return $this->handleResponse($response);
    }

    public function putAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->put($this->getUrl($path, true), $data);

        return $this->handleResponse($response);
    }

    public function putBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->put($this->getUrl($path, true, true), $data);

        return $this->handleResponse($response);
    }

    public function delete(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->delete($this->getUrl($path), $data);

        return $this->handleResponse($response);
    }

    public function deleteAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->delete($this->getUrl($path, true), $data);

        return $this->handleResponse($response);
    }

    public function deleteBulk(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request()->delete($this->getUrl($path, true, true), $data);

        return $this->handleResponse($response);
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

    public function available(): bool
    {
        return $this->checksMagento->available($this->connection);
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

    public function intercept(Closure $callable): static
    {
        $this->interceptor = $callable;

        return $this;
    }

    protected function request(): PendingRequest
    {
        $request = $this->request->build($this->connection);

        if (isset($this->interceptor)) {
            call_user_func($this->interceptor, $request);
            $this->interceptor = null;
        }

        return $request;
    }

    protected function handleResponse(Response $response): Response
    {
        MagentoResponseEvent::dispatch($response, $this->connection);

        return $response;
    }

    public static function fake(): void
    {
        config()->set('magento.connection', 'default');
        config()->set('magento.connections.default', [
            'base_url' => 'magento',
            'base_path' => 'rest',
            'graphql_path' => 'graphql',
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
