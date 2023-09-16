<?php

namespace JustBetter\MagentoClient\Requests;

use Illuminate\Support\LazyCollection;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Concerns\LazilyRetrievesPages;
use JustBetter\MagentoClient\Query\SearchCriteria;

/** @deprecated  */
class Orders
{
    use LazilyRetrievesPages;

    public function __construct(protected Magento $magento)
    {
    }

    /** @return LazyCollection<int, array> */
    public function lazy(SearchCriteria $searchCriteria = null, int $pageSize = 100): LazyCollection
    {
        return $this->retrieveLazily('orders', $pageSize, $searchCriteria);
    }

    public function loadByIncrementId(string $incrementId): ?array
    {
        $searchCriteria = SearchCriteria::make()
            ->where('increment_id', $incrementId)
            ->get();

        $orders = $this->magento->get('orders', $searchCriteria)
            ->json('items');

        if (count($orders) !== 1) {
            return null;
        }

        return $orders[0];
    }
}
