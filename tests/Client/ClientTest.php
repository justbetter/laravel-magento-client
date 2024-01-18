<?php

namespace JustBetter\MagentoClient\Tests\Client;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Tests\TestCase;

class ClientTest extends TestCase
{
    public function test_it_can_make_a_graphql_call(): void
    {
        Http::fake([
            'graphql' => Http::response([
                'data' => [
                    'currency' => [
                        'base_currency_code' => 'EUR'
                    ]
                ]
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->store('default')->graphql('query { currency { base_currency_code } }', []);

        $this->assertEquals(true, $response->ok());
        $this->assertEquals('EUR', $response->json('data.currency.base_currency_code'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && $request->url() == 'magento/graphql'
                && $request->body() === '{"query":"query { currency { base_currency_code } }","variables":[]}';
        });
    }

    public function test_it_can_make_a_get_call(): void
    {
        Http::fake([
            'magento/rest/all/V1/products*' => Http::response(['items' => []]),
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
                $request->url() == 'magento/rest/all/V1/products?searchCriteria%5BpageSize%5D=10&searchCriteria%5BcurrentPage%5D=0';
        });
    }

    public function test_it_can_make_a_post_call(): void
    {
        Http::fake([
            'magento/rest/all/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
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
                $request->url() == 'magento/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_an_async_post_call(): void
    {
        Http::fake([
            'magento/rest/all/async/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->postAsync('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST' &&
                $request->url() == 'magento/rest/all/async/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_bulk_post_call(): void
    {
        Http::fake([
            'magento/rest/all/async/bulk/V1/products' => Http::response([
                'bulk_uuid' => Str::uuid()->toString(),
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 1,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->postBulk('products', [
            [
                'product' => [
                    'sku' => '::sku-1::',
                ],
            ],
            [
                'product' => [
                    'sku' => '::sku-2::',
                ],
            ],
        ]);

        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('request_items'));

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'POST' &&
                $request->url() == 'magento/rest/all/async/bulk/V1/products' &&
                $request->body() === '[{"product":{"sku":"::sku-1::"}},{"product":{"sku":"::sku-2::"}}]';
        });
    }

    public function test_it_can_make_a_put_call(): void
    {
        Http::fake([
            'magento/rest/all/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
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
                $request->url() == 'magento/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_an_async_put_call(): void
    {
        Http::fake([
            'magento/rest/all/async/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->putAsync('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PUT' &&
                $request->url() == 'magento/rest/all/async/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_bulk_put_call(): void
    {
        Http::fake([
            'magento/rest/all/async/bulk/V1/products' => Http::response([
                'bulk_uuid' => Str::uuid()->toString(),
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 1,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->putBulk('products', [
            [
                'product' => [
                    'sku' => '::sku-1::',
                ],
            ],
            [
                'product' => [
                    'sku' => '::sku-2::',
                ],
            ],
        ]);

        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('request_items'));

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'PUT' &&
                $request->url() == 'magento/rest/all/async/bulk/V1/products' &&
                $request->body() === '[{"product":{"sku":"::sku-1::"}},{"product":{"sku":"::sku-2::"}}]';
        });
    }

    public function test_it_can_make_a_patch_call(): void
    {
        Http::fake([
            'magento/rest/all/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
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
                $request->url() == 'magento/rest/all/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_an_async_patch_call(): void
    {
        Http::fake([
            'magento/rest/all/async/V1/products' => Http::response([
                'product' => [
                    'entity_id' => 1,
                    'sku' => '::some-sku::',
                ],
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->patchAsync('products', [
            'product' => [
                'sku' => '::some-sku::',
            ],
        ]);
        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('product'));

        Http::assertSent(function (Request $request) {
            return $request->method() === 'PATCH' &&
                $request->url() == 'magento/rest/all/async/V1/products' &&
                $request->body() === '{"product":{"sku":"::some-sku::"}}';
        });
    }

    public function test_it_can_make_a_bulk_patch_call(): void
    {
        Http::fake([
            'magento/rest/all/async/bulk/V1/products' => Http::response([
                'bulk_uuid' => Str::uuid()->toString(),
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 1,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->patchBulk('products', [
            [
                'product' => [
                    'sku' => '::sku-1::',
                ],
            ],
            [
                'product' => [
                    'sku' => '::sku-2::',
                ],
            ],
        ]);

        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('request_items'));

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'PATCH' &&
                $request->url() == 'magento/rest/all/async/bulk/V1/products' &&
                $request->body() === '[{"product":{"sku":"::sku-1::"}},{"product":{"sku":"::sku-2::"}}]';
        });
    }

    public function test_it_can_make_a_delete_call(): void
    {
        Http::fake([
            'magento/rest/all/V1/products/1' => Http::response([], 204),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->delete('products/1');
        $this->assertEquals(204, $response->status());

        Http::assertSent(function (Request $request) {
            return $request->method() === 'DELETE' &&
                $request->url() == 'magento/rest/all/V1/products/1';
        });
    }

    public function test_it_can_make_an_async_delete_call(): void
    {
        Http::fake([
            'magento/rest/all/async/V1/products/1' => Http::response([], 204),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->deleteAsync('products/1');
        $this->assertEquals(204, $response->status());

        Http::assertSent(function (Request $request) {
            return $request->method() === 'DELETE' &&
                $request->url() == 'magento/rest/all/async/V1/products/1';
        });
    }

    public function test_it_can_make_a_bulk_delete_call(): void
    {
        Http::fake([
            'magento/rest/all/async/bulk/V1/products/bySku' => Http::response([
                'bulk_uuid' => Str::uuid()->toString(),
                'request_items' => [
                    [
                        'id' => 0,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                    [
                        'id' => 1,
                        'data_hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                        'status' => 'accepted',
                    ],
                ],
                'errors' => false,
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $response = $magento->deleteBulk('products/bySku', [
            [
                'sku' => '::sku-1::',
            ],
            [
                'sku' => '::sku-2::',
            ],
        ]);

        $this->assertEquals(true, $response->ok());
        $this->assertCount(2, $response->json('request_items'));

        Http::assertSent(function (Request $request): bool {
            return $request->method() === 'DELETE' &&
                $request->url() == 'magento/rest/all/async/bulk/V1/products/bySku' &&
                $request->body() === '[{"sku":"::sku-1::"},{"sku":"::sku-2::"}]';
        });
    }

    public function test_it_can_get_results_lazily(): void
    {
        Http::fake([
            'magento/rest/all/V1/products?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=sku&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=neq&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=something&searchCriteria%5BpageSize%5D=5&searchCriteria%5BcurrentPage%5D=1' => Http::response([
                'items' => [
                    ['sku' => '1000'],
                    ['sku' => '2000'],
                    ['sku' => '3000'],
                    ['sku' => '4000'],
                    ['sku' => '5000'],
                ],
            ]),
            'magento/rest/all/V1/products?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=sku&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=neq&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=something&searchCriteria%5BpageSize%5D=5&searchCriteria%5BcurrentPage%5D=2' => Http::response([
                'items' => [
                    ['sku' => '6000'],
                    ['sku' => '7000'],
                ],
            ]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $products = $magento->lazy('products', [
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'neq',
            'searchCriteria[filter_groups][0][filters][0][value]' => 'something',
        ], 5);

        $collection = $products->collect();

        $this->assertEquals(7, $collection->count());
    }

    public function test_it_can_set_store_code(): void
    {
        Http::fake([
            'magento/rest/store/V1/products*' => Http::response(['items' => []]),
        ]);

        /** @var Magento $magento */
        $magento = app(Magento::class);

        $magento->store('store')->get('products');

        Http::assertSent(function (Request $request) {
            return $request->url() == 'magento/rest/store/V1/products';
        });
    }
}
