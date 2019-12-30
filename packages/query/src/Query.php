<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Utilities\Arr;
use Windwalker\Utilities\Classes\FlowControlTrait;
use Windwalker\Utilities\Classes\MarcoableTrait;

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
     * Query constructor.
     *
     * @param  mixed|\PDO  $connection
     */
    public function __construct($connection)
    {
        if (!$connection instanceof \WeakReference) {
            $connection = new \WeakReference($connection);
        }

        $this->connection = $connection;
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

    public function quote($value)
    {
        // todo: add pstorm.meta
        return $value;
    }

    public function quoteName($name)
    {
        // todo: add pstorm.meta
        return $name;
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
}
