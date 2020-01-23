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
 * Interface ConnectionInterface
 */
interface ConnectionInterface
{
    /**
     * connect
     *
     * @return  mixed
     */
    public function connect();

    /**
     * disconnect
     *
     * @return  mixed
     */
    public function disconnect();

    /**
     * isConnected
     *
     * @return  bool
     */
    public function isConnected(): bool;

    /**
     * @return mixed
     */
    public function get();
}
