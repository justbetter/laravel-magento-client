<?php

namespace JustBetter\MagentoClient\Client;

use Illuminate\Http\Client\Response;
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

    public function patch(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->patch($this->getUrl($path), $data);

        return $response;
    }

    public function put(string $path, array $data = []): Response
    {
        /** @var Response $response */
        $response = $this->request->build()->put($this->getUrl($path), $data);

        return $response;
    }

    public function getUrl(string $path): string
    {
        return implode('/', [
            config('magento.base_path', 'rest'),
            $this->storeCode ?? config('magento.store_code', 'all'),
            config('magento.version', 'V1'),
            $path,
        ]);
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
        ]);
    }
}
