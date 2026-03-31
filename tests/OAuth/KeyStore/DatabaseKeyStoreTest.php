<?php

declare(strict_types=1);

namespace JustBetter\MagentoClient\Tests\OAuth\KeyStore;

use Illuminate\Support\Facades\Storage;
use JustBetter\MagentoClient\Models\OAuthKey;
use JustBetter\MagentoClient\OAuth\KeyStore\DatabaseKeyStore;
use JustBetter\MagentoClient\OAuth\KeyStore\KeyStore;
use JustBetter\MagentoClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class DatabaseKeyStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        config()->set('magento.oauth.keystore', DatabaseKeyStore::class);
    }

    #[Test]
    public function it_can_get_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        $store = KeyStore::instance();

        OAuthKey::query()->create([
            'magento_connection' => 'default',
            'keys' => $content,
        ]);

        $data = $store->get('default');

        $this->assertSame($content, $data);
    }

    #[Test]
    public function it_can_set_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        $store = KeyStore::instance();
        $store->set('default', $content);

        $data = $store->get('default');

        $this->assertSame($content, $data);
    }

    #[Test]
    public function it_can_merge_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        OAuthKey::query()->create([
            'magento_connection' => 'default',
            'keys' => $content,
        ]);

        $store = KeyStore::instance();

        $new = [
            'something' => 'else',
        ];

        $store->merge('default', $new);

        $data = $store->get('default');

        $this->assertSame(array_merge($content, $new), $data);
    }

    #[Test]
    public function it_can_handle_multiple_connections(): void
    {
        $store = KeyStore::instance();

        $store->set('connection_one', ['one']);
        $store->set('connection_two', ['two']);

        $this->assertSame(['one'], $store->get('connection_one'));
        $this->assertSame(['two'], $store->get('connection_two'));
    }
}
