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
use Windwalker\Query\Clause\ValueClause;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Utilities\Arr;
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
 * @method Clause|null getWhere()
 * @method Query[]     getSubQueries()
 * @method string|null getAlias()
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
    protected $where;

    /**
     * @var array
     */
    protected $subQueries = [];

    /**
     * @var Grammar
     */
    protected $grammar;

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
     * Todo: Change to escaper if need
     * @var mixed|\PDO
     */
    protected $connection;

    /**
     * Query constructor.
     *
     * @param  mixed|\PDO    $connection
     * @param  Grammar|null  $grammar
     */
    public function __construct($connection = null, Grammar $grammar = null)
    {
        $this->connection = $connection;
        $this->grammar    = $grammar ?: new Grammar();
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
        if (!$this->select) {
            $this->type   = static::TYPE_SELECT;
            $this->select = $this->clause('SELECT', [], ', ');
        }

        $columns = array_map(
            [$this, 'as'],
            array_values(Arr::flatten($columns))
        );

        $this->select->append($columns);

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
        return $this->select(raw($this->as($column, $alias)));
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
                $value($value = $this->createNewInstance());
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

    public function where($column, $operator = null, $value = null, string $glue = 'AND')
    {
        if (!$this->where) {
            $this->where = $this->clause('WHERE');
        }

        if ($column instanceof \Closure) {
            $this->handleNestedWheres(
                $column,
                $this->where->elements !== [] ? strtoupper($glue) : ''
            );

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
            $operator,
            $value,
            count(func_get_args()) === 2
        );

        $this->where->append(
            $this->clause(
                // First where should not have prefix
                $this->where->elements !== [] ? strtoupper($glue) : '',
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
     * handleOperatorAndValue
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

        if ($value instanceof \Closure) {
            $value($value = $this->createNewInstance());
        }

        $origin = $value;

        if ($value === null) {
            if ($operator === '=') {
                $operator = 'IS';
            } elseif ($operator === '!=') {
                $operator = 'IS NOT';
            }

            $value = $this->val(raw('NULL'));
        } elseif (is_array($value)) {
            if ($operator === '=') {
                $operator = 'IN';
            } elseif ($operator === '!=') {
                $operator = 'NOT IN';
            }

            $value = $this->clause('()', [], ', ');

            foreach ($origin as $val) {
                $value->append($vc = $this->val($val));

                $this->bindValue(null, $vc);
            }
        } elseif ($value instanceof static) {
            $value = $this->val($value);
            $this->injectSubQuery($origin);
        } elseif ($value instanceof RawWrapper) {
            $value = $this->val($value());
        } else {
            $this->bindValue(null, $value = $this->val($value));
        }

        return [strtoupper($operator), $value, $origin];
    }

    private function handleNestedWheres(\Closure $callback, string $glue): void
    {
        $query = $this->createNewInstance();

        $callback($query);

        $where = $query->getWhere();

        $this->where->append($where->setName($glue . ' ()'));

        foreach ($query->getBounded() as $key => $param) {
            if (TypeCast::tryInteger($key, true) !== null) {
                $this->bounded[] = $param;
            } else {
                $this->bounded[$key] = $param;
            }
        }
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
     * @param  string|array|WrapperInterface  $value
     *
     * @return  string|array
     */
    public function escape($value)
    {
        $value = value($value);

        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->escape($v);
            }

            return $value;
        }

        return Escaper::escape($this->getConnection(), $value);
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
        if ($value instanceof RawWrapper) {
            return value($value);
        }

        if (is_array($value)) {
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

        return Escaper::quote($this->getConnection(), (string) $value);
    }

    /**
     * quoteName
     *
     * @param  string|array|WrapperInterface  $name
     *
     * @return  string|array
     */
    public function quoteName($name)
    {
        if ($name instanceof RawWrapper) {
            return value($name);
        }

        if ($name === '*') {
            return $name;
        }

        if ($name instanceof Clause) {
            return $name->setElements($this->quoteName($name->elements));
        }

        if (is_array($name)) {
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
        $bounded = $this->mergeBounded();

        $method = 'compile' . ucfirst($this->type);

        $sql = $this->getGrammar()->$method($this);

        if ($emulatePrepared) {
            $sql = Escaper::replaceQueryParams($this->getConnection(), $sql, $bounded);
        }

        $this->sequence = null;

        return $sql;
    }

    public function mergeBounded(?BoundedSequence $sequence = null): array
    {
        $sequence = $sequence ?: new BoundedSequence('wqp__');

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
     * @return  \PDO|mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function getConnection()
    {
        if ($this->connection instanceof \WeakReference) {
            return $this->connection->get();
        }

        return $this->connection;
    }

    /**
     * Method to set property connection
     *
     * @param  \PDO|\WeakReference|mixed  $connection
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

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
     * Method to provide deep copy support to nested objects and arrays
     * when cloning.
     *
     * @return  void
     */
    // public function __clone()
    // {
    //     foreach (get_object_vars($this) as $k => $v) {
    //         if (is_object($v) || is_array($v)) {
    //             $this->{$k} = unserialize(serialize($v));
    //         }
    //     }
    // }

    /**
     * createNewInstacne
     *
     * @return  static
     */
    protected function createNewInstance(): self
    {
        return new static($this->connection, $this->grammar);
    }

    public function __call(string $name, array $args)
    {
        $field = lcfirst(substr($name, 3));

        if (property_exists($this, $field)) {
            return $this->$field;
        }

        throw new \BadMethodCallException(
            sprintf('Call to undefined method of: %s::%s()', static::class, $name)
        );
    }
}
