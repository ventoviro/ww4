<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Database\Driver\AbstractConnection;
use Windwalker\Database\Driver\Pdo\AbstractPdoConnection;
use Windwalker\Database\Test\Driver\AbstractConnectionTest;

/**
 * The AbstractPdoConnectionTest class.
 */
abstract class AbstractPdoConnectionTest extends AbstractConnectionTest
{
    /**
     * assertConnected
     *
     * @param  AbstractPdoConnection  $conn
     *
     * @return  void
     */
    public function assertConnected(AbstractConnection $conn): void
    {
        $pdo = $conn->get();

        $r = $pdo->query('SELECT 1')->fetch(\PDO::FETCH_NUM);

        self::assertEquals([1], $r);
    }
}
