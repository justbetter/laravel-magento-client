<?php

namespace JustBetter\MagentoClient\Tests\Requests;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JustBetter\MagentoClient\Requests\Orders;
use JustBetter\MagentoClient\Tests\TestCase;

class OrdersTest extends TestCase
{
    public function test_it_can_retrieve_by_increment_id(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/orders*' => Http::response([
                'items' => [
                    [
                        'increment_id' => '::increment_id::',
                    ],
                ],
            ]),
        ]);

        /** @var Orders $orderRequest */
        $orderRequest = app(Orders::class);

        $order = $orderRequest->loadByIncrementId('::increment_id::');

        /** @phpstan-ignore-next-line */
        $this->assertEquals('::increment_id::', $order['increment_id']);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'http://magento.test/rest/all/V1/orders?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=increment_id&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=eq&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=%3A%3Aincrement_id%3A%3A';
        });
    }

    public function test_it_returns_null_when_order_does_not_exist(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/orders*' => Http::response([
                'items' => [],
            ]),
        ]);

        /** @var Orders $orderRequest */
        $orderRequest = app(Orders::class);

        $order = $orderRequest->loadByIncrementId('::increment_id::');

        $this->assertNull($order);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'http://magento.test/rest/all/V1/orders?searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bfield%5D=increment_id&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bcondition_type%5D=eq&searchCriteria%5Bfilter_groups%5D%5B0%5D%5Bfilters%5D%5B0%5D%5Bvalue%5D=%3A%3Aincrement_id%3A%3A';
        });
    }

    public function test_it_can_lazily_retrieve_orders(): void
    {
        Http::fake([
            'http://magento.test/rest/all/V1/orders*' => Http::sequence([
                Http::response([
                    'items' => [['increment_id' => '::increment_id_1::']],
                    'total_count' => 2,
                ]),
                Http::response([
                    'items' => [['increment_id' => '::increment_id_2::']],
                    'total_count' => 2,
                ]),
            ]),
        ]);

        /** @var Orders $orderRequest */
        $orderRequest = app(Orders::class);

        $retrievedOrders = [];

        foreach ($orderRequest->lazy(null, 1) as $order) {
            $retrievedOrders[] = $order['increment_id'];
        }

        $this->assertCount(2, $retrievedOrders);
    }
}
