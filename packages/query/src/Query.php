<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Bounded\BoundedSequence;
use Windwalker\Query\Bounded\ParamType;
use Windwalker\Query\Clause\AsClause;
use Windwalker\Query\Clause\Clause;
use Windwalker\Query\Clause\ClauseInterface;
use Windwalker\Query\Clause\JoinClause;
use Windwalker\Query\Clause\ValueClause;
use Windwalker\Query\Expression\Expression;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Assert\ArgumentsAssert;
use Windwalker\Utilities\Classes\FlowControlTrait;
use Windwalker\Utilities\Classes\MarcoableTrait;
use Windwalker\Utilities\TypeCast;
use Windwalker\Utilities\Wrapper\RawWrapper;
use Windwalker\Utilities\Wrapper\WrapperInterface;

use function Windwalker\raw;
use function Windwalker\value;

/**
 * The Query class.
 *
 * @method string|null getType()
 * @method Clause|null getSelect()
 * @method Clause|null getFrom()
 * @method Clause|null getJoin()
 * @method Clause|null getUnion()
 * @method Clause|null getWhere()
 * @method Clause|null getHaving()
 * @method Clause|null getOrder()
 * @method Clause|null getGroup()
 * @method Clause|null getLimit()
 * @method Clause|null getOffset()
 * @method Clause|null getInsert()
 * @method Clause|null getColumns()
 * @method Clause|null getValues()
 * @method Query[]     getSubQueries()
 * @method string|null getAlias()
 * @method $this leftJoin($table, ?string $alias, ...$on)
 * @method $this rightJoin($table, ?string $alias, ...$on)
 * @method $this outerJoin($table, ?string $alias, ...$on)
 * @method $this innerJoin($table, ?string $alias, ...$on)
 * @method $this whereIn($column, array $values)
 * @method $this whereNotIn($column, array $values)
 * @method $this whereBetween($column, $start, $end)
 * @method $this whereNotBetween($column, $start, $end)
 * @method $this whereLike($column, string $search)
 * @method $this whereNotLike($column, string $search)
 * @method $this havingIn($column, array $values)
 * @method $this havingNotIn($column, array $values)
 * @method $this havingBetween($column, $start, $end)
 * @method $this havingNotBetween($column, $start, $end)
 * @method $this havingLike($column, string $search)
 * @method $this havingNotLike($column, string $search)
 * @method string|array qn($text)
 * @method string|array q($text)
 */
class Query implements QueryInterface
{
    use MarcoableTrait;
    use FlowControlTrait;

    public const TYPE_SELECT = 'select';

    public const TYPE_INSERT = 'insert';

    public const TYPE_UPDATE = 'update';

    public const TYPE_DELETE = 'delete';

    public const TYPE_UNION = 'union';

    public const TYPE_CUSTOM = 'custom';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Clause
     */
    protected $select;

    /**
     * @var Clause
     */
    protected $from;

    /**
     * @var Clause
     */
    protected $join;

    /**
     * @var Clause
     */
    protected $union;

    /**
     * @var Clause
     */
    protected $where;

    /**
     * @var Clause
     */
    protected $having;

    /**
     * @var Clause
     */
    protected $order;

    /**
     * @var Clause
     */
    protected $group;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var Clause
     */
    protected $insert;

    /**
     * @var Clause
     */
    protected $columns;

    /**
     * @var Clause
     */
    protected $values;

    /**
     * @var string
     */
    protected $incrementField;

    /**
     * @var array
     */
    protected $subQueries = [];

    /**
     * @var Grammar
     */
    protected $grammar;

    /**
     * @var Expression
     */
    protected $expression;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var array
     */
    protected $bounded = [];

    /**
     * @var BoundedSequence
     */
    protected $sequence;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Query constructor.
     *
     * @param  mixed|\PDO|Escaper  $escaper
     * @param  Grammar|null        $grammar
     */
    public function __construct($escaper = null, Grammar $grammar = null)
    {
        $this->grammar = $grammar ?: new Grammar();

        $this->setEscaper($escaper);
    }

    /**
     * select
     *
     * @param  mixed  ...$columns
     *
     * @return  static
     */
    public function select(...$columns)
    {
        foreach (array_values(Arr::flatten($columns)) as $column) {
            $this->selectAs($column);
        }

        return $this;
    }

    /**
     * selectAs
     *
     * @param  string|RawWrapper  $column
     * @param  string|null        $alias
     *
     * @return  static
     */
    public function selectAs($column, ?string $alias = null)
    {
        if (!$this->select) {
            $this->type   = static::TYPE_SELECT;
            $this->select = $this->clause('SELECT', [], ', ');
        }

        $this->select->append($this->as($column, $alias));

        return $this;
    }

    /**
     * from
     *
     * @param  string|array  $tables
     * @param  string|null   $alias
     *
     * @return  static
     */
    public function from($tables, ?string $alias = null)
    {
        if ($this->from === null) {
            $this->from = $this->clause('FROM', [], ', ');
        }

        if (!is_array($tables) && $alias !== null) {
            $tables = [$alias => $tables];
        }

        if (is_array($tables)) {
            foreach ($tables as $tableAlias => $table) {
                $this->from->append($this->as($table, $tableAlias));
            }
        } else {
            $this->from->append($this->as($tables, $alias));
        }

        return $this;
    }

    /**
     * join
     *
     * @param  string                        $type
     * @param  string|Query|ClauseInterface  $table
     * @param  string                        $alias
     * @param  array                         $on
     *
     * @return  static
     */
    public function join(string $type, $table, ?string $alias, ...$on)
    {
        if (!$this->join) {
            $this->join = $this->clause('', [], ' ');
        }

        $tbl      = $this->as($table, $alias);
        $joinType = strtoupper($type) . ' JOIN';

        $join = new JoinClause($this, $joinType, $tbl);

        if (count($on) === 1) {
            ArgumentsAssert::assert(
                $on[0] instanceof \Closure,
                '%s if only has 1 on condition, it must be Closure, %s given.',
                $on[0]
            );

            $on[0]($join);
        } elseif (count($on) <= 3) {
            $join->on(...$on);
        } else {
            ArgumentsAssert::assert(
                count($on) % 3 === 0,
                '%s if on is not callback, it must be 3 times as many, currently is %s.',
                count($on)
            );

            foreach (array_chunk($on, 3) as $cond) {
                $join->on(...$cond);
            }
        }

        $this->join->append($join);

        return $this;
    }

    /**
     * Handle column and sub query.
     *
     * @param  string|array|Query  $value     The column or sub query object.
     * @param  string|bool|null    $alias     The alias string, if this arg provided, will override sub query
     *                                        self-contained alias, if is FALSE, will force ignore alias
     *                                        from aub query.
     * @param  bool                $isColumn  Quote value as column or string.
     *
     * @return  AsClause
     */
    public function as($value, $alias = null, bool $isColumn = true): AsClause
    {
        $quoteMethod = $isColumn ? 'quoteName' : 'quote';
        $clause      = new AsClause();

        if ($value instanceof RawWrapper) {
            $clause->value($value());
        } else {
            if ($value instanceof \Closure) {
                $value($value = $this->createSubQuery());
            }

            if ($value instanceof static) {
                $alias = $alias ?? $value->getAlias();

                $this->injectSubQuery($value, $alias);

                $clause->value($value);
            } else {
                $clause->value((string) $this->$quoteMethod($value));
            }
        }

        // Only column need alias, ignore it if is value.
        if ($isColumn && $alias !== false && (string) $alias !== '') {
            $clause->alias($this->quoteName($alias));
        }

        return $clause;
    }

    /**
     * Add a query to UNION with the current query.
     * Multiple unions each require separate statements and create an array of unions.
     *
     * @param  mixed   $query  The Query object or string to union.
     * @param  string  $type   The union type, can be `DISTINCT` or `ALL`, default is empty.
     *
     * @return  static    The Query object on success or boolean false on failure.
     *
     * @since   2.0
     */
    public function union($query, string $type = '')
    {
        $this->type = static::TYPE_SELECT;

        if (is_array($query)) {
            foreach ($query as $q) {
                $this->union($q, $type);
            }

            return $this;
        }

        // Clear any ORDER BY clause in UNION query
        // See http://dev.mysql.com/doc/refman/5.0/en/union.html
        // if (null !== $this->order) {
        //     $this->clear(['order', 'group']);
        // }

        // Create the Clause if it does not exist
        if (null === $this->union) {
            $prefix = 'UNION';

            if ($type !== '') {
                $prefix .= ' ' . $type;
            }

            $this->union = $this->clause($prefix . ' ()', [], ') ' . $prefix . ' (');
        }

        if ($query instanceof self) {
            $this->injectSubQuery($query, false);
        }

        $this->union->append($query);

        return $this;
    }

    /**
     * Add a query to UNION DISTINCT with the current query. Simply a proxy to Union with the Distinct clause.
     *
     * Usage:
     * $query->unionDistinct('SELECT name FROM  #__foo')
     *
     * @param  mixed  $query  The Query object or string to union.
     *
     * @return  mixed   The Query object on success or boolean false on failure.
     *
     * @since   2.0
     */
    public function unionDistinct($query)
    {
        // Apply the distinct flag to the union.
        return $this->union($query, 'DISTINCT');
    }

    /**
     * Add a query to UNION ALL with the current query.
     * Multiple unions each require separate statements and create an array of unions.
     *
     * Usage:
     * $query->unionAll('SELECT name FROM  #__foo')
     * $query->unionAll(array('SELECT name FROM  #__foo','SELECT name FROM  #__bar'))
     *
     * @param  mixed  $query  The Query object or string to union.
     *
     * @return  mixed  The Query object on success or boolean false on failure.
     *
     * @see     union
     *
     * @since   2.0
     */
    public function unionAll($query)
    {
        // Apply the distinct flag to the union.
        return $this->union($query, 'ALL');
    }

    /**
     * where
     *
     * @param  string|array|\Closure|ClauseInterface  $column  Column name, array where list or callback
     *                                                         function as sub query.
     * @param  mixed                                  ...$args
     *
     * @return  static
     */
    public function where($column, ...$args)
    {
        if ($column instanceof \Closure) {
            $this->handleNestedWheres($column, (string) ($args[0] ?? 'AND'));

            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $where) {
                $this->where(...$where);
            }

            return $this;
        }

        $column = $this->as($column, false);

        [$operator, $value] = $this->handleOperatorAndValue(
            $args[0] ?? null,
            $args[1] ?? null,
            count($args) === 1
        );

        $this->whereRaw(
            $this->clause(
                '',
                [$column, $operator, $value]
            )
        );

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
            $value($value = $this->createSubQuery());
        }

        // Keep origin value a duplicate that we will need it later.
        // The $value will make it s a ValueClause object and inject to bounded params,
        // so that we can use it to generate prepared param placeholders.
        // The $origin variable is to store origin value at Query object if needed.
        $origin = $value;

        if ($value === null) {
            // Process NULL
            if ($operator === '=') {
                $operator = 'IS';
            } elseif ($operator === '!=') {
                $operator = 'IS NOT';
            }

            $value = $this->val(raw('NULL'));
        } elseif (in_array(strtolower($operator), ['between', 'not between'], true)) {
            // Process BETWEEN
            ArgumentsAssert::assert(
                is_array($value) && COUNT($value) === 2,
                'Between should have at least and only 2 values'
            );

            $value = $this->clause('', [], ' AND ');

            foreach ($origin as $val) {
                // Append every value as ValueObject so that we can make placeholders as `IN(?, ?, ?...)`
                $value->append($vc = $this->val($val));

                $this->bindValue(null, $vc);
            }
        } elseif (is_array($value)) {
            // Auto convert array value as IN() clause.
            if ($operator === '=') {
                $operator = 'IN';
            } elseif ($operator === '!=') {
                $operator = 'NOT IN';
            }

            $value = $this->clause('()', [], ', ');

            foreach ($origin as $val) {
                // Append every value as ValueObject so that we can make placeholders as `IN(?, ?, ?...)`
                $value->append($vc = $this->val($val));

                $this->bindValue(null, $vc);
            }
        } elseif ($value instanceof static) {
            // Process Aub query object
            $value = $this->val($value);
            $this->injectSubQuery($origin);
        } elseif ($value instanceof RawWrapper) {
            // Process Raw
            $value = $this->val($value);
        } else {
            // Process simple value compare
            $this->bindValue(null, $value = $this->val($value));
        }

        return [strtoupper($operator), $value, $origin];
    }

    private function handleNestedWheres(\Closure $callback, string $glue, string $type = 'where'): void
    {
        if (!in_array(strtolower(trim($glue)), ['and', 'or'], true)) {
            throw new \InvalidArgumentException('WHERE glue should only be `OR`, `AND`.');
        }

        $callback($query = $this->createSubQuery());

        /** @var Clause $where */
        $where = $query->$type;

        // If where clause not exists, means this callback has no where call, just return.
        if (!$where) {
            return;
        }

        $this->{$type . 'Raw'}(
            $where->setName('()')
                ->setGlue(' ' . strtoupper($glue) . ' ')
        );

        foreach ($query->getBounded() as $key => $param) {
            if (TypeCast::tryInteger($key, true) !== null) {
                $this->bounded[] = $param;
            } else {
                $this->bounded[$key] = $param;
            }
        }
    }

    /**
     * whereRaw
     *
     * @param  string|Clause  $string
     * @param  array          ...$args
     *
     * @return  static
     */
    public function whereRaw($string, ...$args)
    {
        if (!$this->where) {
            $this->where = $this->clause('WHERE', [], ' AND ');
        }

        if (is_string($string) && $args !== []) {
            $string = $this->format($string, ...$args);
        }

        $this->where->append($string);

        return $this;
    }

    /**
     * orWhere
     *
     * @param  array|\Closure  $wheres
     *
     * @return  static
     */
    public function orWhere($wheres)
    {
        if (is_array($wheres)) {
            return $this->orWhere(
                static function (Query $query) use ($wheres) {
                    foreach ($wheres as $where) {
                        $query->where(...$where);
                    }
                }
            );
        }

        ArgumentsAssert::assert(
            $wheres instanceof \Closure,
            '%s argument should be array or Closure, %s given.',
            $wheres
        );

        return $this->where($wheres, 'OR');
    }

    public function having($column, ...$args)
    {
        if ($column instanceof \Closure) {
            $this->handleNestedWheres($column, (string) ($args[0] ?? 'AND'), 'having');

            return $this;
        }

        if (is_array($column)) {
            foreach ($column as $where) {
                $this->having(...$where);
            }

            return $this;
        }

        $column = $this->as($column, false);

        [$operator, $value] = $this->handleOperatorAndValue(
            $args[0] ?? null,
            $args[1] ?? null,
            count($args) === 1
        );

        $this->havingRaw(
            $this->clause(
                '',
                [$column, $operator, $value]
            )
        );

        return $this;
    }

    /**
     * havingRaw
     *
     * @param  string|Clause  $string
     * @param  array          ...$args
     *
     * @return  static
     */
    public function havingRaw($string, ...$args)
    {
        if (!$this->having) {
            $this->having = $this->clause('HAVING', [], ' AND ');
        }

        if (is_string($string) && $args !== []) {
            $string = $this->format($string, ...$args);
        }

        $this->having->append($string);

        return $this;
    }

    /**
     * orWhere
     *
     * @param  array|\Closure  $wheres
     *
     * @return  static
     */
    public function orHaving($wheres)
    {
        if (is_array($wheres)) {
            return $this->orHaving(
                static function (Query $query) use ($wheres) {
                    foreach ($wheres as $where) {
                        $query->having(...$where);
                    }
                }
            );
        }

        ArgumentsAssert::assert(
            $wheres instanceof \Closure,
            '%s argument should be array or Closure, %s given.',
            $wheres
        );

        return $this->having($wheres, 'OR');
    }

    private function whereVariant($type, $operator, array $args)
    {
        $maps = [
            'notin' => 'not in',
            'notbetween' => 'not between',
            'notlike' => 'not like',
        ];

        $operator = strtolower($operator);

        $operator = $maps[$operator] ?? $operator;

        $arg1 = array_shift($args);

        if (in_array($operator, ['between', 'not between'], true)) {
            ArgumentsAssert::assert(
                count($args) === 2,
                'BETWEEN or NOT BETWEEN needs 2 values'
            );

            return $this->$type($arg1, $operator, $args);
        }

        $arg2 = array_shift($args);

        return $this->$type($arg1, $operator, $arg2);
    }

    /**
     * order
     *
     * @param  array|string  $column
     * @param  string        $dir
     *
     * @return  static
     */
    public function order($column, ?string $dir = null)
    {
        if (!$this->order) {
            $this->order = $this->clause('ORDER BY', [], ', ');
        }

        if (is_array($column)) {
            foreach ($column as $col) {
                if (!is_array($col)) {
                    $col = [$col];
                }

                $this->order(...$col);
            }

            return $this;
        }

        $order = [$this->quoteName($column)];

        if ($dir !== null) {
            ArgumentsAssert::assert(
                in_array($dir = strtoupper($dir), ['ASC', 'DESC'], true),
                '%s argument 2 should be one of ASC/DESC, %s given',
                $dir
            );

            $order[] = $dir;
        }

        $this->order->append(implode(' ', $order));

        return $this;
    }

    /**
     * group
     *
     * @param  string|array  ...$columns
     *
     * @return  static
     */
    public function group(...$columns)
    {
        if (!$this->group) {
            $this->group = $this->clause('GROUP BY', [], ', ');
        }

        $this->group->append(
            $this->quoteName(
                array_values(Arr::flatten($columns))
            )
        );

        return $this;
    }

    /**
     * limit
     *
     * @param  int  $limit
     *
     * @return  static
     */
    public function limit(?int $limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * offset
     *
     * @param  int  $offset
     *
     * @return  static
     */
    public function offset(?int $offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * insert
     *
     * @param  string  $table
     * @param  string  $incrementField
     *
     * @return  static
     */
    public function insert(string $table, ?string $incrementField = null)
    {
        $this->type = static::TYPE_INSERT;
        $this->insert = $this->clause('INSERT INTO', $this->quoteName($table));
        $this->incrementField = $incrementField;

        return $this;
    }

    /**
     * columns
     *
     * @param  mixed  ...$columns
     *
     * @return  static
     */
    public function columns(...$columns)
    {
        if (!$this->columns) {
            $this->columns = $this->clause('COLUMNS ()', [], ', ');
        }

        $this->columns->append(
            $this->quoteName(
                array_values(Arr::flatten($columns))
            )
        );

        return $this;
    }

    /**
     * values
     *
     * @param  mixed  ...$values
     *
     * @return  static
     */
    public function values(...$values)
    {
        if (!$this->values) {
            $this->values = $this->clause('VALUES ', [], ', ');
        }

        foreach ($values as $value) {
            ArgumentsAssert::assert(
                is_iterable($value),
                '%s values element should always be array or iterable, %s given.'
            );

            $clause = $this->clause('()', [], ', ');

            foreach ($value as $val) {
                $clause->append($this->handleWriteValue($val));
            }

            $this->values->append($clause);
        }

        return $this;
    }

    private function handleWriteValue($value)
    {
        $origin = $value;

        if ($value === null) {
            $value = $this->val(raw('NULL'));
        } elseif ($value instanceof static) {
            // Process Aub query object
            $value = $this->val($value);
            $this->injectSubQuery($origin);
        } elseif ($value instanceof RawWrapper) {
            // Process Raw
            $value = $this->val($value);
        } else {
            ArgumentsAssert::assert(
                !is_array($value) && !is_object($value),
                'Write values should be scalar or NULL, %2$s given.',
                $value
            );

            // Process simple value compare
            $this->bindValue(null, $value = $this->val($value));
        }

        return $value;
    }

    /**
     * injectSubQuery
     *
     * @param  Query             $query
     * @param  string|bool|null  $alias
     *
     * @return  void
     */
    private function injectSubQuery(Query $query, $alias = null): void
    {
        $alias = $alias ?: $query->getAlias() ?: uniqid('sq');

        $this->subQueries[$alias] = $query;
    }

    /**
     * clause
     *
     * @param  string        $name
     * @param  array|string  $elements
     * @param  string        $glue
     *
     * @return  Clause
     */
    public function clause(string $name, $elements = [], string $glue = ' '): Clause
    {
        return clause($name, $elements, $glue);
    }

    /**
     * escape
     *
     * @param  string|iterable|WrapperInterface  $value
     *
     * @return  string|array
     */
    public function escape($value)
    {
        $value = value($value);

        if (is_iterable($value)) {
            foreach ($value as &$v) {
                $v = $this->escape($v);
            }

            return $value;
        }

        return $this->getEscaper()->escape((string) $value);
    }

    /**
     * quote
     *
     * @param  mixed|WrapperInterface  $value
     *
     * @return  string|array
     */
    public function quote($value)
    {
        $value = value($value);

        if (is_iterable($value)) {
            foreach ($value as &$v) {
                $v = $this->quote($v);
            }

            return $value;
        }

        if ($value === null) {
            return 'NULL';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        return $this->getEscaper()->quote((string) $value);
    }

    /**
     * quoteName
     *
     * @param  string|iterable|WrapperInterface  $name
     *
     * @return  string|array
     */
    public function quoteName($name)
    {
        if ($name instanceof RawWrapper) {
            return value($name);
        }

        if ($name instanceof Clause) {
            return $name->setElements($this->quoteName($name->elements));
        }

        if (is_iterable($name)) {
            foreach ($name as &$n) {
                $n = $this->quoteName($n);
            }

            return $name;
        }

        return $this->getGrammar()->quoteName((string) $name);
    }

    /**
     * alias
     *
     * @param  string  $alias
     *
     * @return  static
     */
    public function alias(string $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function nullDate(): string
    {
        return $this->getGrammar()->nullDate();
    }

    /**
     * Find and replace sprintf-like tokens in a format string.
     * Each token takes one of the following forms:
     *     %%       - A literal percent character.
     *     %[t]     - Where [t] is a type specifier.
     *     %[n]$[x] - Where [n] is an argument specifier and [t] is a type specifier.
     *
     * Types:
     * a - Numeric: Replacement text is coerced to a numeric type but not quoted or escaped.
     * e - Escape: Replacement text is passed to $this->escape().
     * E - Escape (extra): Replacement text is passed to $this->escape() with true as the second argument.
     * n - Name Quote: Replacement text is passed to $this->quoteName().
     * q - Quote: Replacement text is passed to $this->quote().
     * Q - Quote (no escape): Replacement text is passed to $this->quote() with false as the second argument.
     * r - Raw: Replacement text is used as-is. (Be careful)
     *
     * Date Types:
     * - Replacement text automatically quoted (use uppercase for Name Quote).
     * - Replacement text should be a string in date format or name of a date column.
     * y/Y - Year
     * m/M - Month
     * d/D - Day
     * h/H - Hour
     * i/I - Minute
     * s/S - Second
     *
     * Invariable Types:
     * - Takes no argument.
     * - Argument index not incremented.
     * t - Replacement text is the result of $this->currentTimestamp().
     * z - Replacement text is the result of $this->nullDate(false).
     * Z - Replacement text is the result of $this->nullDate(true).
     *
     * Usage:
     * $query->format('SELECT %1$n FROM %2$n WHERE %3$n = %4$a', 'foo', '#__foo', 'bar', 1);
     * Returns: SELECT `foo` FROM `#__foo` WHERE `bar` = 1
     *
     * Notes:
     * The argument specifier is optional but recommended for clarity.
     * The argument index used for unspecified tokens is incremented only when used.
     *
     * @param  string  $format  The formatting string.
     * @param  array   $args    The strings variables.
     *
     * @return  string  Returns a string produced according to the formatting string.
     *
     * @note    This method is a modified version from Joomla DatabaseQuery.
     *
     * @since   2.0
     */
    public function format(string $format, ...$args): string
    {
        $query = $this;
        array_unshift($args, null);

        $expression = $this->getExpression();

        $i    = 1;
        $func = function ($match) use ($query, $args, &$i, $expression) {
            if (isset($match[6]) && $match[6] === '%') {
                return '%';
            }

            // No argument required, do not increment the argument index.
            switch ($match[5]) {
                case 't':
                    return $expression->currentTimestamp();
                    break;

                case 'z':
                    return $query->nullDate();
                    break;

                case 'Z':
                    return $this->quote($query->nullDate());
                    break;
            }

            // Increment the argument index only if argument specifier not provided.
            $index = is_numeric($match[4]) ? (int) $match[4] : $i++;

            if (!$index || !isset($args[$index])) {
                $replacement = '';
            } else {
                $replacement = $args[$index];
            }

            switch ($match[5]) {
                case 'a':
                    return 0 + $replacement;
                    break;

                case 'e':
                    return $query->escape($replacement);
                    break;

                // case 'E':
                //     return $query->escape($replacement, true);
                //     break;

                case 'n':
                    return $query->quoteName($replacement);
                    break;

                case 'q':
                    return $query->quote($replacement);
                    break;

                // case 'Q':
                //     return $query->quote($replacement, false);
                //     break;

                case 'r':
                    return $replacement;
                    break;

                // Dates
                case 'y':
                    return $expression->year($query->quote($replacement));
                    break;

                case 'Y':
                    return $expression->year($query->quoteName($replacement));
                    break;

                case 'm':
                    return $expression->month($query->quote($replacement));
                    break;

                case 'M':
                    return $expression->month($query->quoteName($replacement));
                    break;

                case 'd':
                    return $expression->day($query->quote($replacement));
                    break;

                case 'D':
                    return $expression->day($query->quoteName($replacement));
                    break;

                case 'h':
                    return $expression->hour($query->quote($replacement));
                    break;

                case 'H':
                    return $expression->hour($query->quoteName($replacement));
                    break;

                case 'i':
                    return $expression->minute($query->quote($replacement));
                    break;

                case 'I':
                    return $expression->minute($query->quoteName($replacement));
                    break;

                case 's':
                    return $expression->second($query->quote($replacement));
                    break;

                case 'S':
                    return $expression->second($query->quoteName($replacement));
                    break;
            }

            return '';
        };

        /**
         * Regexp to find an replace all tokens.
         * Matched fields:
         * 0: Full token
         * 1: Everything following '%'
         * 2: Everything following '%' unless '%'
         * 3: Argument specifier and '$'
         * 4: Argument specifier
         * 5: Type specifier
         * 6: '%' if full token is '%%'
         */
        return preg_replace_callback('#%(((([\d]+)\$)?([aeEnqQryYmMdDhHiIsStzZ]))|(%))#', $func, $format);
    }

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param  string|integer|array  $key            The key that will be used in your SQL query to reference the value.
     *                                               Usually of the form ':key', but can also be an integer.
     * @param  mixed                &$value          The value that will be bound. The value is passed by reference to
     *                                               support output parameters such as those possible with stored
     *                                               procedures.
     * @param  mixed                 $dataType       Constant corresponding to a SQL datatype.
     * @param  integer               $length         The length of the variable. Usually required for OUTPUT parameters.
     * @param  array                 $driverOptions  Optional driver options to be used.
     *
     * @return  static
     *
     * @since   3.5.5
     */
    public function bind(
        $key = null,
        &$value = null,
        $dataType = null,
        int $length = 0,
        $driverOptions = null
    ) {
        // If is array, loop for all elements.
        if (is_array($key)) {
            foreach ($key as $k => &$v) {
                $this->bind($k, $v, $dataType, $length, $driverOptions);
            }

            return $this;
        }

        if ($dataType === null) {
            $dataType = ParamType::guessType(
                $value instanceof ValueClause ? $value->getValue() : $value
            );
        }

        $bounded = [
            'value' => &$value,
            'dataType' => $dataType,
            'length' => $length,
            'driverOptions' => $driverOptions,
        ];

        if ($key === null) {
            $this->bounded[] = $bounded;
        } else {
            $this->bounded[$key] = $bounded;
        }

        return $this;
    }

    /**
     * Method to add a variable to an internal array that will be bound to a prepared SQL statement before query
     * execution. Also removes a variable that has been bounded from the internal bounded array when the passed in
     * value is null.
     *
     * @param  string|integer|array  $key            The key that will be used in your SQL query to reference the
     *                                               value. Usually of the form ':key', but can also be an integer.
     * @param  mixed                &$value          The value that will be bound. The value is passed by reference to
     *                                               support output parameters such as those possible with stored
     *                                               procedures.
     * @param  mixed                 $dataType       Constant corresponding to a SQL datatype.
     * @param  integer               $length         The length of the variable. Usually required for OUTPUT
     *                                               parameters.
     * @param  array                 $driverOptions  Optional driver options to be used.
     *
     * @return  static
     *
     * @since   2.0
     */
    public function bindValue(
        $key = null,
        $value = null,
        $dataType = null,
        int $length = 0,
        $driverOptions = null
    ) {
        return $this->bind($key, $value, $dataType, $length, $driverOptions);
    }

    /**
     * Retrieves the bound parameters array when key is null and returns it by reference. If a key is provided then
     * that item is returned.
     *
     * @param  mixed  $key  The bounded variable key to retrieve.
     *
     * @return  array|null
     *
     * @since   2.0
     */
    public function &getBounded($key = null): ?array
    {
        if (empty($key)) {
            return $this->bounded;
        }

        return $this->bounded[$key] ?? null;
    }

    /**
     * resetBounded
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function resetBounded()
    {
        $this->bounded = [];

        return $this;
    }

    /**
     * unbind
     *
     * @param  string|array  $keys
     *
     * @return  static
     *
     * @since  3.5.12
     */
    public function unbind($keys)
    {
        $keys = (array) $keys;

        $this->bounded = array_diff_key($this->bounded, array_flip($keys));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->render();
    }

    public function render(bool $emulatePrepared = false, ?array &$bounded = []): string
    {
        // Only top level query rendering should create sequence and get merged bounded
        if (!$this->sequence) {
            $bounded = $this->mergeBounded();
        }

        $method = 'compile' . ucfirst($this->type);

        $sql = $this->getGrammar()->$method($this);

        if ($emulatePrepared) {
            $sql = Escaper::replaceQueryParams($this->getEscaper(), $sql, $bounded);
        }

        // Clear sequence so that next time rendering should re-create new one
        $this->sequence = null;

        return $sql;
    }

    public function getMergedBounded(): array
    {
        $bounded = $this->mergeBounded();

        $this->sequence = null;

        return $bounded;
    }

    private function mergeBounded(?BoundedSequence $sequence = null): array
    {
        $this->sequence = $sequence = $sequence ?: new BoundedSequence('wqp__');

        $all     = [];
        $bounded = [];

        $params = $this->getBounded();

        foreach ($params as $key => $param) {
            if ($param['value'] instanceof ValueClause) {
                $param['value']->setPlaceholder($sequence->get());
                $key            = $param['value']->getPlaceholder();
                $param['value'] = $param['value']->getValue();

                $bounded[$key] = $param;
            } else {
                $bounded[$key] = $param;
            }
        }

        $all[] = $bounded;

        foreach ($this->getSubQueries() as $subQuery) {
            $all[] = $subQuery->mergeBounded($sequence);
        }

        return array_merge(...$all);
    }

    /**
     * getSubQuery
     *
     * @param  string  $alias
     *
     * @return  Query|null
     */
    public function getSubQuery(string $alias): ?Query
    {
        return $this->subQueries[$alias] ?? null;
    }

    /**
     * Method to get property Connection
     *
     * @return  Escaper
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getEscaper()
    {
        return $this->escaper;
    }

    /**
     * Method to set property connection
     *
     * @param  Escaper|\PDO|\WeakReference|mixed  $escaper
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setEscaper($escaper)
    {
        $this->escaper = $escaper instanceof Escaper ? $escaper : new Escaper($escaper, $this);

        return $this;
    }

    /**
     * Method to get property Grammar
     *
     * @return  Grammar
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getGrammar(): Grammar
    {
        return $this->grammar;
    }

    /**
     * getExpression
     *
     * @return  Expression
     */
    public function getExpression(): Expression
    {
        if ($this->expression) {
            return $this->expression;
        }

        $class = sprintf(__NAMESPACE__ . '\\Expression\\' . $this->grammar::getName() . 'Expression');

        if (!class_exists($class)) {
            $class = Expression::class;
        }

        return $this->expression = new $class($this);
    }

    /**
     * Method to provide deep copy support to nested objects and arrays
     * when cloning.
     *
     * @return  void
     */
    public function __clone()
    {
        foreach (get_object_vars($this) as $k => $v) {
            if (is_object($v)) {
                $this->{$k} = clone $v;
            }
        }
    }

    /**
     * createSubQuery
     *
     * @return  static
     */
    public function createSubQuery(): self
    {
        return new static($this->escaper, $this->grammar);
    }

    /**
     * Clear data from the query or a specific clause of the query.
     *
     * @param  string|array  $clauses  Optionally, the name of the clause to clear, or nothing to clear the whole query.
     *
     * @return static  Returns this object to allow chaining.
     *
     * @since   2.0
     */
    public function clear($clauses = null)
    {
        $this->sql = null;

        if (is_array($clauses)) {
            foreach ($clauses as $clause) {
                $this->clear($clause);
            }

            return $this;
        }

        // TODO: rewrite clear process

        switch ($clauses) {
            case 'select':
                $this->select = null;
                $this->type   = null;
                break;

            case 'delete':
                $this->delete = null;
                $this->type   = null;
                break;

            case 'update':
                $this->update = null;
                $this->type   = null;
                break;

            case 'insert':
                $this->insert             = null;
                $this->type               = null;
                $this->autoIncrementField = null;
                break;

            case 'from':
                $this->from = null;
                break;

            case 'join':
                $this->join = null;
                break;

            case 'set':
                $this->set = null;
                break;

            case 'where':
                $this->where = null;
                break;

            case 'group':
                $this->group = null;
                break;

            case 'having':
                $this->having = null;
                break;

            case 'order':
                $this->order = null;
                break;

            case 'columns':
                $this->columns = null;
                break;

            case 'values':
                $this->values = null;
                break;

            case 'exec':
                $this->exec = null;
                $this->type = null;
                break;

            case 'call':
                $this->call = null;
                $this->type = null;
                break;

            case 'limit':
                $this->offset = 0;
                $this->limit  = 0;
                break;

            case 'suffix':
                $this->suffix = null;
                break;

            case 'union':
                $this->union = null;
                break;

            case 'alias':
                $this->alias = null;
                break;

            default:
                $this->type               = null;
                $this->select             = null;
                $this->delete             = null;
                $this->update             = null;
                $this->insert             = null;
                $this->from               = null;
                $this->join               = null;
                $this->set                = null;
                $this->where              = null;
                $this->group              = null;
                $this->having             = null;
                $this->order              = null;
                $this->columns            = null;
                $this->values             = null;
                $this->autoIncrementField = null;
                $this->exec               = null;
                $this->call               = null;
                $this->union              = null;
                $this->offset             = 0;
                $this->limit              = 0;
                $this->suffix             = null;
                $this->bounded            = [];
                $this->alias              = null;
                break;
        }

        return $this;
    }

    public function __call(string $name, array $args)
    {
        // Simple Alias
        $aliases = [
            'qn' => 'quoteName',
            'q' => 'quote',
        ];

        if (in_array($name, $aliases, true)) {
            return $this->{$aliases[$name]}(...$args);
        }

        // Get Fields
        $field = lcfirst(substr($name, 3));

        if (property_exists($this, $field)) {
            return $this->$field;
        }

        // Where/Having
        if (strpos($name, 'where') === 0) {
            $operator = substr($name, 5);

            return $this->whereVariant('where', $operator, $args);
        }

        if (strpos($name, 'having') === 0) {
            $operator = substr($name, 6);

            return $this->whereVariant('having', $operator, $args);
        }

        // Join
        $aliases = [
            'leftJoin' => 'LEFT',
            'rightJoin' => 'RIGHT',
            'innerJoin' => 'INNER',
            'outerJoin' => 'OUTER',
            'crossJoin' => 'CROSS',
        ];

        if (isset($aliases[$name])) {
            return $this->join($aliases[$name], ...$args);
        }

        throw new \BadMethodCallException(
            sprintf('Call to undefined method of: %s::%s()', static::class, $name)
        );
    }
}
