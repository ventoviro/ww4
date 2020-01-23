<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Driver;

use Windwalker\Query\Query;

/**
 * Interface DriverInterface
 */
interface DriverInterface
{
    /**
     * connect
     *
     * @return  ConnectionInterface
     */
    public function connect(): ConnectionInterface;

    /**
     * disconnect
     *
     * @return  mixed
     */
    public function disconnect();

    /**
     * Prepare a statement.
     *
     * @param  string|Query $query
     * @param  array  $options
     *
     * @return  StatementInterface
     */
    public function prepare($query, array $options = []): StatementInterface;

    /**
     * Execute a query.
     *
     * @param string|Query $query
     *
     * @return  bool
     */
    public function execute($query): bool;

    /**
     * Quote and escape a value.
     *
     * @param  string  $value
     *
     * @return  string
     */
    public function quote(string $value): string;

    /**
     * Escape a value.
     *
     * @param  string  $value
     *
     * @return  string
     */
    public function escape(string $value): string;
}