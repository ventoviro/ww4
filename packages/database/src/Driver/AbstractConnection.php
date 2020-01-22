<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

/**
 * The AbstractConnection class.
 */
abstract class AbstractConnection
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var mixed
     */
    protected $connection;

    /**
     * AbstractConnection constructor.
     *
     * @param  array  $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * connect
     *
     * @return  mixed
     */
    abstract public function connect();

    /**
     * disconnect
     *
     * @return  mixed
     */
    abstract public function disconnect();

    /**
     * @return \WeakReference
     */
    public function getConnection(): \WeakReference
    {
        return \WeakReference::create($this->connection);
    }
}
