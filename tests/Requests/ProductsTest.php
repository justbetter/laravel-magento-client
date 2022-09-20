<?php

namespace JustBetter\MagentoClient\Tests\Requests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Requests\Products;
use JustBetter\MagentoClient\Tests\TestCase;

class ProductsTest extends TestCase
{
    public function test_it_can_retrieve_a_page(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products*' => Http::response([
                'items' => [['sku' => '::some_sku::']],
                'total_count' => 1,
            ]),
        ]);

        /** @var Products $products */
        $productsRequest = app(Products::class); /** @phpstan-ignore-line */
        $retrievedProducts = $productsRequest->retrieve(0, 50);

        $this->assertEquals(1, $retrievedProducts['total_count']);
        $this->assertEquals('::some_sku::', $retrievedProducts['items'][0]['sku']);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'http://magento.test/rest/all/V1/products?searchCriteria%5BpageSize%5D=50&searchCriteria%5BcurrentPage%5D=0';
        });
    }

    public function test_it_can_lazily_retrieve_products(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/products*' => Http::sequence([
                Http::response([
                    'items' => [['sku' => '::sku_1::']],
                    'total_count' => 2,
                ]),
                Http::response([
                    'items' => [['sku' => '::sku_2::']],
                    'total_count' => 2,
                ]),
            ]),
        ]);

        /** @var Products $products */
        $productsRequest = app(Products::class); /** @phpstan-ignore-line */
        $lazyProducts = $productsRequest->lazy(null, 1);

        $retrievedSkus = [];

        foreach ($lazyProducts as $product) {
            $retrievedSkus[] = $product['sku'];
        }

        $this->assertCount(2, $retrievedSkus);
    }
}
