<?php

namespace JustBetter\MagentoClient\Query;

class SearchCriteria
{
    public array $pagination = [];

    public array $select = [];

    public array $wheres = [];

    protected int $currentFilterGroup = 0;

    protected int $currentFilterIndex = 0;

    public function __construct(protected Grammar $grammar)
    {
    }

    public function paginate(int $page, int $pageSize): static
    {
        $this->pagination = [
            'searchCriteria[pageSize]' => $pageSize,
            'searchCriteria[currentPage]' => $page,
        ];

        return $this;
    }

    public function select(array $fields): static
    {
        $this->select['fields'] = implode(',', $fields);

        return $this;
    }

    public function where(
        string $field,
        string $operator,
        mixed $value = null,
        ?int $filterGroup = null,
        int $filterIndex = 0
    ): static {
        // Assume the operator is equals
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $this->grammar->checkOperator($operator);

        $currentFilterGroup = $filterGroup ?? $this->currentFilterGroup;

        $prefix = "searchCriteria[filter_groups][$currentFilterGroup][filters][$filterIndex]";

        $this->wheres[$prefix.'[field]'] = $field;
        $this->wheres[$prefix.'[condition_type]'] = $this->grammar->getOperator($operator);
        $this->wheres[$prefix.'[value]'] = $value;

        if ($filterGroup === null) {
            $this->currentFilterGroup++;
        }

        if ($filterIndex === 0) {
            $this->currentFilterIndex = 0;
        }

        return $this;
    }

    public function orWhere(string $field, string $operator, mixed $value = null): static
    {
        $this->currentFilterIndex++;

        $this->where(
            $field,
            $operator,
            $value,
            $this->currentFilterGroup > 0 ? $this->currentFilterGroup - 1 : 0,
            $this->currentFilterIndex
        );

        return $this;
    }

    public function whereIn(string $field, array $values): static
    {
        return $this->where($field, 'in', implode(',', $values));
    }

    public function whereNotIn(string $field, array $values): static
    {
        return $this->where($field, 'nin', implode(',', $values));
    }

    public function get(): array
    {
        return array_merge(
            $this->select,
            $this->wheres,
            $this->pagination,
        );
    }

    public function dd(): void
    {
        dd($this->get());
    }

    public static function make(): static
    {
        return app(static::class);
    }
}
