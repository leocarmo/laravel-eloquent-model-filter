<?php

namespace LeoCarmo\ModelFilter\Traits;

trait FilterableModel
{

    /**
     * Default operator for filters
     *
     * @var string
     */
    protected $default_operator = '=';

    /**
     * Filter model based on request filters
     * Use prop $filterable on model for allowed filters
     *
     * @param array $filters
     * @return \Illuminate\Database\Query\Builder
     */
    public function filter(array $filters)
    {

        // new query
        $query = $this->newQuery();

        // Select required fields
        $query->select(
            $this->getFilterableSelect()
        );

        // Each filterable set on model will be checked
        foreach ($this->getFilterable() as $filterable) {

            // Check if filter was sent and if is not empty
            if (isset($filters[$filterable]) && ! empty($filters[$filterable])) {

                // get custom operator if exists
                $operator = $this->filterable[$filterable] ?? $this->default_operator;

                // push new where operation to query
                $this->pushWhereToQuery($query, $filterable, $filters[$filterable], $operator);

            }

        }

        // Return query
        return $query;
    }

    /**
     * Push new elements to select query
     *
     * @param $select
     * @return $this
     */
    public function pushFilterableSelect($select)
    {
        if (! isset($this->filterable_select)) {

            $this->filterable_select = [];

        }

        if (is_array($select)) {

            $this->filterable_select = array_merge($this->filterable_select, $select);
            return $this;

        }

        $this->filterable_select[] = $select;
        return $this;
    }

    /**
     * Push new allowed filterable to query
     *
     * @param $filterable
     * @return $this
     */
    public function pushFilterable($filterable)
    {
        if (! isset($this->filterable)) {

            $this->filterable = [];

        }

        if (is_array($filterable)) {

            $this->filterable = array_merge($this->filterable, $filterable);
            return $this;

        }

        $this->filterable[] = $filterable;
        return $this;
    }

    /**
     * Change default operator for filters
     * Default: =
     *
     * @param string $operator
     * @return $this
     */
    public function changeDefaultOperator(string $operator)
    {
        $this->default_operator = $operator;
        return $this;
    }

    /**
     * Privates methods
     */

    /**
     * Get attributes to filter
     *
     * @return array
     */
    private function getFilterable()
    {
        // check if filterable attribute was defined
        if (! $this->filterable) {
            $this->filterable = [];
        }

        // get all keys
        $array_keys = array_keys($this->filterable);

        // check if array is sequential
        if ($array_keys === range(0, count($this->filterable) - 1)) {

            return array_unique($this->filterable);

        }

        // elements to return (array is assoc)
        $return_elements = [];

        // push correct element to return
        foreach ($this->filterable as $key => $obj) {

            $return_elements[] = is_numeric($key) ? $obj : $key;

        }

        // return results
        return array_unique($return_elements);
    }

    /**
     * Push where to query
     *
     * @param $query
     * @param $filterable
     * @param $search
     * @param $operator
     */
    private function pushWhereToQuery(&$query, $filterable, $search, $operator)
    {

        switch (mb_strtoupper($operator)) {

            case 'LIKE':

                $query->where(
                    $filterable,
                    'LIKE',
                    "%{$search}%"
                );
                break;

            default:

                $query->where(
                    $filterable,
                    $operator,
                    $search
                );
                break;

        }

    }

    /**
     * Get select attributes to search
     *
     * @return string|array
     */
    private function getFilterableSelect()
    {
        if ($this->filterable_select) {
            return array_unique($this->filterable_select);
        }

        return '*';
    }

}