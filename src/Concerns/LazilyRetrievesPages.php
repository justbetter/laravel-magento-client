<?php

namespace JustBetter\MagentoClient\Concerns;

use Illuminate\Support\LazyCollection;
use JustBetter\MagentoClient\Client\Magento;
use JustBetter\MagentoClient\Query\SearchCriteria;

/** @property Magento $magento */
trait LazilyRetrievesPages
{
    public function retrieveLazily(string $endpoint, int $pageSize, ?SearchCriteria $searchCriteria = null): LazyCollection
    {
        /** @phpstan-ignore-next-line */
        return LazyCollection::make(function () use ($endpoint, $pageSize, $searchCriteria) {
            $currentPage = 1;

            $search = ($searchCriteria ?? SearchCriteria::make());

            $hasNextPage = true;

            while ($hasNextPage) {
                $response = $this->magento
                    ->get($endpoint, $search->paginate($currentPage, $pageSize)->get())
                    ->throw();

                yield from $response->json('items');

                $hasNextPage = $currentPage * $pageSize < $response->json('total_count');
                $currentPage++;
            }
        });
    }
}
