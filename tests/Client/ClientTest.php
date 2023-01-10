<?php

namespace JustBetter\MagentoClient\Tests\Client;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_it_can_make_a_get_call(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products*' => Http::response(['items' => []]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->get('products', [
            'searchCriteria[pageSize]' => 10,
            'searchCriteria[currentPage]' => 0,
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(0, $response->json('items'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'GET' &&
                $request->url() == 'http://magento.test/rest/all/V1/products?searchCriteria%5BpageSize%5D=10&searchCriteria%5BcurrentPage%5D=0';
        });
    }

    public function test_it_can_make_a_post_call(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products' => Http::response(['product' => ['entity_id' => 1, 'sku' => '::some-sku::']]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->post('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->url() == 'http://magento.test/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_put_call(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products' => Http::response(['product' => ['entity_id' => 1, 'sku' => '::some-sku::']]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->put('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PUT' &&
                $request->url() == 'http://magento.test/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_patch_call(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products' => Http::response(['product' => ['entity_id' => 1, 'sku' => '::some-sku::']]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->patch('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PATCH' &&
                $request->url() == 'http://magento.test/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_delete_call(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products/1' => Http::response([], 204),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->delete('products/1');
        $this->assertEquals(204, $response->status());

        Http::assertSent(function (Request $request) {
            return $request->method() === 'DELETE' &&
                $request->url() == 'http://magento.test/rest/all/V1/products/1';
        });
    }

    public function test_it_can_set_store_code(): void
    {
        Http::fake([
            'http://magento.test/rest/store/V1/products*' => Http::response(['items' => []]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $magento->store('store')->get('products');

        Http::assertSent(function (Request $request) {
            return $request->url() == 'http://magento.test/rest/store/V1/products';
        });
    }
}
