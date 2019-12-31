<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Clause\Clause;
use Windwalker\Query\Grammar\Grammar;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Assert\ArgumentsAssert;
use Windwalker\Utilities\Classes\FlowControlTrait;
use Windwalker\Utilities\Classes\MarcoableTrait;
use Windwalker\Utilities\Wrapper\RawWrapper;
use Windwalker\Utilities\Wrapper\WrapperInterface;
use function Windwalker\raw;
use function Windwalker\value;

/**
 * The Query class.
 *
 * @property-read string $type
 * @property-read Clause $select
 * @property-read Clause $from
 * @property-read array  $subQueries
 * @property-read string $alias
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
        $new = clone $this;

        if (!$new->select) {
            $new->type   = static::TYPE_SELECT;
            $new->select = $this->clause('SELECT', [], ', ');
        }

        $columns = array_map(
            [$this, 'as'],
            array_values(Arr::flatten($columns))
        );

        $new->select->append($columns);

        return $new;
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
        $new = clone $this;

        if ($new->from === null) {
            $new->from = $this->clause('FROM', [], ', ');
        }

        if (!is_array($tables) && $alias !== null) {
            $tables = [$alias => $tables];
        }

        if (is_array($tables)) {
            foreach ($tables as $tableAlias => $table) {
                $new->from->append(
                    $this->clause(
                        '',
                        [
                            $this->quoteName($this->tryWrap($table)),
                            'AS',
                            $this->quoteName($tableAlias)
                        ]
                    )
                );
            }
        } else {
            $new->from->append($this->as($tables, $alias));
        }

        return $new;
    }

    public function as($value, ?string $alias = null): string
    {
        if ($value instanceof RawWrapper) {
            $value = $value();
        } elseif ($value instanceof static) {
            if ($value->alias) {
                $alias = $value->alias;
            }

            $value = '(' . $value . ')';
        } else {
            $value = (string) $this->quoteName($value);
        }

        ArgumentsAssert::assert(
            is_stringable($value),
            '%s argument 1 should be stringable or RawWrapper'
        );

        if ($alias) {
            $value .= ' AS ' . $this->quoteName($alias);
        }

        return $value;
    }

    private function tryWrap($value): string
    {
        return $value instanceof static ? '(' . $value . ')' : $value;
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

        return substr(
            substr(
                $this->getConnection()->quote($value),
                0,
                -1
            ),
            1
        );
    }

    /**
     * quote
     *
     * @param  string|array|WrapperInterface  $value
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

        return $this->getConnection()->quote($value);
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
        $new = clone $this;

        $new->alias = $alias;

        return $new;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->render();
    }

    public function render(): string
    {
        $method = 'compile' . ucfirst($this->type);

        return $this->getGrammar()->$method($this);
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
        return value($this->connection);
    }

    /**
     * Method to set property connection
     *
     * @param  \PDO|mixed  $connection
     *
     * @return  static  Return self to support chaining.
     *
     * @since  __DEPLOY_VERSION__
     */
    public function setConnection($connection)
    {
        if (!$connection instanceof \WeakReference) {
            $connection = new \WeakReference($connection);
        }

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

    public function __get(string $name)
    {
        return $this->$name;
    }
}
