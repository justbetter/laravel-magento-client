<?php

namespace JustBetter\MagentoClient\Tests\OAuth\KeyStore;

use Illuminate\Support\Facades\Storage;
use JustBetter\MagentoClient\OAuth\KeyStore\FileKeyStore;
use JustBetter\MagentoClient\OAuth\KeyStore\KeyStore;
use JustBetter\MagentoClient\Tests\TestCase;

class FileKeyStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        config()->set('magento.oauth.keystore', FileKeyStore::class);
    }

    /** @test */
    public function it_can_get_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        /** @var FileKeyStore $action */
        $action = KeyStore::instance();

        /** @var string $encoded */
        $encoded = json_encode($content);

        Storage::disk($action->disk)->put($action->path.'/default.json', $encoded);

        $data = $action->get('default');

        $this->assertEquals($content, $data);
    }

    /** @test */
    public function it_can_set_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        /** @var FileKeyStore $action */
        $action = KeyStore::instance();
        $action->set('default', $content);

        $data = $action->get('default');

        $this->assertEquals($content, $data);
    }

    /** @test */
    public function it_can_merge_data(): void
    {
        $content = [
            'key' => 'value',
        ];

        /** @var string $encoded */
        $encoded = json_encode($content);

        /** @var FileKeyStore $action */
        $action = KeyStore::instance();

        Storage::disk($action->disk)->put($action->path.'/default.json', $encoded);

        $new = [
            'something' => 'else',
        ];

        $action->merge('default', $new);

        $data = $action->get('default');

        $this->assertEquals(array_merge($content, $new), $data);
    }
}
