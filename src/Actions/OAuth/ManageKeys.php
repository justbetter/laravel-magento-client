<?php

namespace JustBetter\MagentoClient\Actions\OAuth;

use Illuminate\Support\Facades\Storage;
use JustBetter\MagentoClient\Contracts\OAuth\ManagesKeys;

class ManageKeys implements ManagesKeys
{
    public function get(): array
    {
        /** @var string $disk */
        $disk = config('magento.oauth.file.disk');

        /** @var string $path */
        $path = config('magento.oauth.file.path');

        $content = Storage::disk($disk)->get($path);

        return json_decode($content, true) ?? [];
    }

    public function set(array $data): void
    {
        /** @var string $disk */
        $disk = config('magento.oauth.file.disk');

        /** @var string $path */
        $path = config('magento.oauth.file.path');

        /** @var string $visibility */
        $visibility = config('magento.oauth.file.visibility');

        Storage::disk($disk)->put($path, json_encode($data), $visibility);
    }

    public function merge(array $data): void
    {
        $merged = array_merge($this->get(), $data);

        $this->set($merged);
    }

    public static function bind(): void
    {
        app()->singleton(ManagesKeys::class, static::class);
    }
}
