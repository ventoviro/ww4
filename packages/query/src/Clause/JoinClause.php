<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Clause;

use Windwalker\Query\Query;
use Windwalker\Utilities\Assert\ArgumentsAssert;
use Windwalker\Utilities\Wrapper\RawWrapper;

/**
 * The JoinClause class.
 */
class JoinClause implements ClauseInterface
{
    /**
     * @var string|AsClause
     */
    protected $table;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var Clause
     */
    protected $on;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * JoinClause constructor.
     *
     * @param  Query            $query
     * @param  string           $prefix
     * @param  string|AsClause  $table
     */
    public function __construct(Query $query, string $prefix, $table)
    {
        $this->table = $table;
        $this->query = $query;
        $this->prefix = $prefix;
    }

    /**
     * on
     *
     * @param  string|array|\Closure|ClauseInterface  $column  Column name, array where list or callback
     *                                                         function as sub query.
     * @param  mixed                                  ...$args
     *
     * @return  static
     */
    public function on($column, ...$args)
    {
        if ($column instanceof \Closure) {
            $this->handleNestedOn($column, (string) ($args[0] ?? 'AND'));

            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $where) {
                $this->on(...$where);
            }

            return $this;
        }

        $column = $this->query->as($column, false);

        [$operator, $value] = $this->handleOperatorAndValue(
            $args[0] ?? null,
            $args[1] ?? null,
            count($args) === 1
        );

        $this->onRaw(
            $this->query->clause(
                '',
                [$column, $operator, $value]
            )
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param  string  $prefix
     *
     * @return  static  Return self to support chaining.
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    private function val($value): ValueClause
    {
        return new ValueClause($value);
    }

    /**
     * Handle value and operator.
     *
     * This method will wrap value as a ValueObject and inject into bounded params.
     * By default, where clause uses `?` as placeholder and bind variables to prepared statement.
     * But it is hard to handle sub queries' placeholder ordering.
     *
     * ValueObject will be injected to bounded temporaries so that we can change `?` to
     * a named param like: `:wqp__{ordering}` and re-calc the order when every time rendering Query object,
     * so we can make sure the variables won't be conflict.
     *
     * @param  mixed  $operator
     * @param  mixed  $value
     * @param  bool   $shortcut
     *
     * @return  array
     */
    private function handleOperatorAndValue($operator, $value, bool $shortcut = false): array
    {
        if ($shortcut) {
            [$operator, $value] = ['=', $operator];
        }

        if ($operator === null) {
            throw new \InvalidArgumentException('Where operator should not be NULL');
        }

        // Closure means to create a sub query as value.
        if ($value instanceof \Closure) {
            $value($value = $this->query->createSubQuery());
        }

        // Keep origin value a duplicate that we will need it later.
        // The $value will make it s a ValueClause object and inject to bounded params,
        // so that we can use it to generate prepared param placeholders.
        // The $origin variable is to store origin value at Query object if needed.
        $origin = $value;

        if ($value === null) {
            if ($operator === '=') {
                $operator = 'IS';
            } elseif ($operator === '!=') {
                $operator = 'IS NOT';
            }

            $value = 'NULL';
        } elseif (is_array($value)) {
            // Auto convert array value as IN() clause.
            if ($operator === '=') {
                $operator = 'IN';
            } elseif ($operator === '!=') {
                $operator = 'NOT IN';
            }

            $value = $this->query->clause('()', [], ', ');

            foreach ($origin as $col) {
                // Append every value as ValueObject so that we can make placeholders as `IN(?, ?, ?...)`
                $value->append($vc = $this->query->quote($col));

                $this->query->bindValue(null, $vc);
            }
        } elseif ($value instanceof Query) {
            $value = $this->query->as($origin, false);
        } elseif ($value instanceof RawWrapper) {
            $value = $value();
        } else {
            $value = $this->query->quoteName($value);
        }

        return [strtoupper($operator), $value, $origin];
    }

    private function handleNestedOn(\Closure $callback, string $glue): void
    {
        if (!in_array(strtolower(trim($glue)), ['and', 'or'], true)) {
            throw new \InvalidArgumentException('WHERE glue should only be `OR`, `AND`.');
        }

        $callback($clause = new static($this->query, $this->prefix, $this->table));

        /** @var Clause $clause */
        $clause = $clause->on;

        // If where clause not exists, means this callback has no where call, just return.
        if (!$clause) {
            return;
        }

        $this->onRaw(
            $clause->setName('()')
                ->setGlue(' ' . strtoupper($glue) . ' ')
        );
    }

    /**
     * orWhere
     *
     * @param  array|\Closure  $wheres
     *
     * @return  static
     */
    public function orOn($wheres)
    {
        if (is_array($wheres)) {
            return $this->orOn(
                static function (JoinClause $join) use ($wheres) {
                    foreach ($wheres as $where) {
                        $join->on(...$where);
                    }
                }
            );
        }

        ArgumentsAssert::assert(
            $wheres instanceof \Closure,
            '%s argument should be array or Closure, %s given.',
            $wheres
        );

        return $this->on($wheres, 'OR');
    }

    /**
     * onRaw
     *
     * @param  string|Clause  $condition
     * @param  mixed          ...$args
     *
     * @return  static
     */
    public function onRaw($condition, ...$args)
    {
        if (!$this->on) {
            $this->on = $this->query->clause('ON', [], ' AND ');
        }

        if (is_string($condition) && $args !== []) {
            $condition = $this->query->format($condition, ...$args);
        }

        $this->on->append($condition);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->prefix . ' ' . $this->table . ' ' . $this->on;
    }

    /**
     * @return string|AsClause
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Method to set property table
     *
     * @param  string|AsClause  $table
     *
     * @return  static  Return self to support chaining.
     */
    public function join($table)
    {
        $this->table = $table;

        return $this;
    }
}
