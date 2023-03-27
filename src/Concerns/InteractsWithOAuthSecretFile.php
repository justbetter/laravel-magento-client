<?php

namespace JustBetter\MagentoClient\Concerns;

use Illuminate\Support\Facades\Storage;

trait InteractsWithOAuthSecretFile
{
    public function read(): ?array
    {
        /** @var string $disk */
        $disk = config('magento.oauth.file.disk');

        /** @var string $path */
        $path = config('magento.oauth.file.path');

        $content = Storage::disk($disk)->get($path);

        return json_decode($content, true);
    }

    public function write(array $data): void
    {
        /** @var string $disk */
        $disk = config('magento.oauth.file.disk');

        /** @var string $path */
        $path = config('magento.oauth.file.path');

        /** @var string $visibility */
        $visibility = config('magento.oauth.file.visibility');

        Storage::disk($disk)->put($path, json_encode($data), $visibility);
    }
}
