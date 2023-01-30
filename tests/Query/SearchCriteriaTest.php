<?php

namespace JustBetter\MagentoClient\Tests\Query;

use Exception;
use JustBetter\MagentoClient\Query\SearchCriteria;
use JustBetter\MagentoClient\Tests\TestCase;
use Symfony\Component\VarDumper\VarDumper;

class SearchCriteriaTest extends TestCase
{
    public function test_it_creates_pagination(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->paginate(1, 10)
            ->get();

        $this->assertEquals([
            'searchCriteria[pageSize]' => 10,
            'searchCriteria[currentPage]' => 1,
        ], $searchCriteria);
    }

    public function test_it_can_add_a_simple_where(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->where('sku', '::some-sku::')
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::some-sku::',
        ], $searchCriteria);
    }

    public function test_it_can_add_multiple_wheres(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->where('sku', '=', '::some-sku::')
            ->where('name', '::some-name::')
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::some-sku::',
            'searchCriteria[filter_groups][1][filters][0][field]' => 'name',
            'searchCriteria[filter_groups][1][filters][0][condition_type]' => 'eq',
            'searchCriteria[filter_groups][1][filters][0][value]' => '::some-name::',
        ], $searchCriteria);
    }

    public function test_it_can_add_orWhere(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->where('sku', '=', '::some-sku::')
            ->orWhere('name', '=', '::some-name::')
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::some-sku::',
            'searchCriteria[filter_groups][0][filters][1][field]' => 'name',
            'searchCriteria[filter_groups][0][filters][1][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][1][value]' => '::some-name::',
        ], $searchCriteria);
    }

    public function test_it_can_add_multiple_orWhere(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->where('sku', '=', '::some-sku::')
            ->orWhere('name', '=', '::some-name::')
            ->where('some_attribute', '>', 10)
            ->orWhere('another_attribute', '<=', 100)
            ->orWhere('test_attribute', '<>', '::some_value::')
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::some-sku::',
            'searchCriteria[filter_groups][0][filters][1][field]' => 'name',
            'searchCriteria[filter_groups][0][filters][1][condition_type]' => 'eq',
            'searchCriteria[filter_groups][0][filters][1][value]' => '::some-name::',
            'searchCriteria[filter_groups][1][filters][0][field]' => 'some_attribute',
            'searchCriteria[filter_groups][1][filters][0][condition_type]' => 'gt',
            'searchCriteria[filter_groups][1][filters][0][value]' => '10',
            'searchCriteria[filter_groups][1][filters][1][field]' => 'another_attribute',
            'searchCriteria[filter_groups][1][filters][1][condition_type]' => 'lteq',
            'searchCriteria[filter_groups][1][filters][1][value]' => '100',
            'searchCriteria[filter_groups][1][filters][2][field]' => 'test_attribute',
            'searchCriteria[filter_groups][1][filters][2][condition_type]' => 'neq',
            'searchCriteria[filter_groups][1][filters][2][value]' => '::some_value::',
        ], $searchCriteria);
    }

    public function test_it_can_add_whereIn(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->whereIn('sku', ['::sku_1::', '::sku_2::', '::sku_3::'])
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'in',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::sku_1::,::sku_2::,::sku_3::',
        ], $searchCriteria);
    }

    public function test_it_can_add_whereNotIn(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->whereNotIn('sku', ['::sku_1::', '::sku_2::', '::sku_3::'])
            ->get();

        $this->assertEquals([
            'searchCriteria[filter_groups][0][filters][0][field]' => 'sku',
            'searchCriteria[filter_groups][0][filters][0][condition_type]' => 'nin',
            'searchCriteria[filter_groups][0][filters][0][value]' => '::sku_1::,::sku_2::,::sku_3::',
        ], $searchCriteria);
    }

    public function test_it_can_add_a_field_selection(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->select(['sku', 'price'])
            ->get();

        $this->assertEquals([
            'fields' => 'sku,price',
        ], $searchCriteria);
    }

    public function test_it_can_dd(): void
    {
        $searchCriteria = SearchCriteria::make()
            ->select(['sku', 'price']);

        $this->expectException(Exception::class);

        VarDumper::setHandler(function (array $data) {
            $this->assertEquals([
                'fields' => 'sku,price',
            ], $data);

            throw new Exception();
        });

        $searchCriteria->dd();
    }
}
