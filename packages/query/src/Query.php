<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Grammar\Grammar;
use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\FlowControlTrait;
use Windwalker\Utilities\Classes\MarcoableTrait;
use Windwalker\Utilities\Wrapper\RawWrapper;

use Windwalker\Utilities\Wrapper\WrapperInterface;

use function Windwalker\value;

/**
 * The Query class.
 */
class Query implements QueryInterface
{
    use MarcoableTrait;
    use FlowControlTrait;

    /**
     * @var \WeakReference
     */
    protected $connection;

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
     * Query constructor.
     *
     * @param  mixed|\PDO    $connection
     * @param  Grammar|null  $grammar
     */
    public function __construct($connection = null, Grammar $grammar = null)
    {
        if (!$connection instanceof \WeakReference) {
            $connection = new \WeakReference($connection);
        }

        $this->connection = $connection;
        $this->grammar = $grammar ?: new Grammar();
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

        $columns = Arr::collapse($columns);

        if (!$new->select) {
            $new->select = new Clause('SELECT');
        }

        $new->select->append(array_values($columns));

        return $new;
    }

    public function from($tables, $alias = null)
    {
        $new = clone $this;

        if ($new->from === null) {
            $new->from = $this->clause('FROM', $tables);
        } else {
            $new->from->append($tables);
        }

        return $new;
    }

    /**
     * clause
     *
     * @param  string  $name
     * @param  array   $elements
     * @param  string  $glue
     *
     * @return  Clause
     */
    public function clause(string $name, array $elements = [], string $glue = ' '): Clause
    {
        return new Clause($name, $elements, $glue);
    }

    /**
     * escape
     *
     * @param string|array|WrapperInterface $value
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
     * @param string|array|WrapperInterface $value
     *
     * @return  string|array
     */
    public function quote($value)
    {
        $value = value($value);

        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->quoteName($v);
            }

            return $value;
        }

        return $this->getConnection()->quote($value);
    }

    /**
     * quoteName
     *
     * @param string|array|WrapperInterface $name
     *
     * @return  string|array
     */
    public function quoteName($name)
    {
        $name = value($name);

        if (is_array($name)) {
            foreach ($name as &$n) {
                $n = $this->quoteName($n);
            }

            return $name;
        }

        return $this->getGrammar()->quoteName($name);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return '';
    }

    /**
     * Method to get property Connection
     *
     * @return  \PDO|mixed
     *
     * @since  __DEPLOY_VERSION__
     */
    public function &getConnection()
    {
        return $this->connection->get();
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
}
