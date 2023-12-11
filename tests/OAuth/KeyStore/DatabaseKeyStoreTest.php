<?php

namespace JustBetter\MagentoClient\Tests\OAuth\KeyStore;

use Illuminate\Support\Facades\Storage;
use JustBetter\MagentoClient\Models\OauthKey;
use JustBetter\MagentoClient\OAuth\KeyStore\DatabaseKeyStore;
use JustBetter\MagentoClient\OAuth\KeyStore\KeyStore;
use JustBetter\MagentoClient\Tests\TestCase;

class DatabaseKeyStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        config()->set('magento.oauth.keystore', DatabaseKeyStore::class);
    }

    /** @test */
    public function it_can_get_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        $store = KeyStore::instance();

        OauthKey::query()->create([
            'magento_connection' => 'default',
            'keys' => $content,
        ]);

        $data = $store->get('default');

        $this->assertEquals($content, $data);
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        $store = KeyStore::instance();
        $store->set('default', $content);

        $data = $store->get('default');

        $this->assertEquals($content, $data);
    }

    /** @test */
    public function it_can_merge_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        OauthKey::query()->create([
            'magento_connection' => 'default',
            'keys' => $content,
        ]);

        $store = KeyStore::instance();

        $new = [
            'something' => 'else',
        ];

        $store->merge('default', $new);

        $data = $store->get('default');

        $this->assertEquals(array_merge($content, $new), $data);
    }

    /** @test */
    public function it_can_handle_multiple_connections(): void
    {
        $store = KeyStore::instance();

        $store->set('connection_one', ['one']);
        $store->set('connection_two', ['two']);

        $this->assertEquals(['one'], $store->get('connection_one'));
        $this->assertEquals(['two'], $store->get('connection_two'));
    }
}
