<?php

namespace JustBetter\MagentoClient\Client;

use Generator;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use JustBetter\MagentoClient\Contracts\BuildsRequest;

class Magento
{
    public ?string $storeCode = null;

    public function __construct(
        protected BuildsRequest $request
    ) {
    }

    public function store(?string $store = null): static
    {
        $this->storeCode = $store;

        return $this;
    }

    public function get(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->get($this->getUrl($path), $data);

        return $response;
    }

    public function post(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->post($this->getUrl($path), $data);

        return $response;
    }

    public function postAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->post($this->getUrl($path, true), $data);

        return $response;
    }

    public function patch(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->patch($this->getUrl($path), $data);

        return $response;
    }

    public function patchAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->patch($this->getUrl($path, true), $data);

        return $response;
    }

    public function put(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->put($this->getUrl($path), $data);

        return $response;
    }

    public function putAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->put($this->getUrl($path, true), $data);

        return $response;
    }

    public function delete(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->delete($this->getUrl($path), $data);

        return $response;
    }

    public function deleteAsync(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->delete($this->getUrl($path, true), $data);

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

    public function getUrl(string $path, bool $async = false): string
    {
        $options =[
            config('magento.base_path', 'rest'),
            $this->storeCode ?? config('magento.store_code', 'all'),
        ];

        if ($async) {
            $options[] = 'async';
        }

        $options[] = config('magento.version', 'V1');
        $options[] = $path;

        return implode('/', $options);
    }

    public static function fake(): void
    {
        config()->set('magento', [
            'base_url' => 'http://magento.test',
            'base_path' => 'rest',
            'store_code' => 'all',
            'version' => 'V1',
            'access_token' => '::token::',
            'timeout' => 30,
            'connect_timeout' => 10,
            'authentication_method' => 'token',
            'oauth' => [
                'middleware' => [],
                'prefix' => 'magento/oauth',
                'file' => [
                    'disk' => 'local',
                    'path' => 'secret/magento2_oauth.json',
                    'visibility' => 'private',
                ],
            ],
        ]);
    }
}
