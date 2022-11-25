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

    public function store(string $store): static
    {
        $this->storeCode = $store;

        return $this;
    }

    public function get(string $path, array $data = []): Response
    {
        return $this->request->build()
            ->get($this->getUrl($path), $data);
    }

    public function post(string $path, array $data = []): Response
    {
        return $this->request->build()
            ->post($this->getUrl($path), $data);
    }

    public function patch(string $path, array $data = []): Response
    {
        return $this->request->build()
            ->patch($this->getUrl($path), $data);
    }

    public function put(string $path, array $data = []): Response
    {
        return $this->request->build()
            ->put($this->getUrl($path), $data);
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
