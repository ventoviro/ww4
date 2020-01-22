<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Mysqli;

use Windwalker\Database\Driver\AbstractConnection;
use Windwalker\Database\Driver\Mysqli\MysqliConnection;
use Windwalker\Database\Test\Driver\AbstractConnectionTest;

/**
 * The MysqliConnectionTest class.
 */
class MysqliConnectionTest extends AbstractConnectionTest
{
    protected static $platform = 'mysql';

    protected static $className = MysqliConnection::class;

    /**
     * assertConnected
     *
     * @param  MysqliConnection  $conn
     *
     * @return  void
     */
    public function assertConnected(AbstractConnection $conn): void
    {
        $mysqli = $conn->getConnection();

        $r = $mysqli->query('SELECT 1')->fetch_row();

        self::assertEquals([1], $r);
    }
}
