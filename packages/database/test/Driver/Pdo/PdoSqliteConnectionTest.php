<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Database\Driver\Pdo\PdoSqliteConnection;

/**
 * The PdoSqliteConnection class.
 */
class PdoSqliteConnectionTest extends AbstractPdoConnectionTest
{
    protected static $platform = 'sqlite';

    protected static $className = PdoSqliteConnection::class;

    public function testConnectWrong()
    {
        $conn = $this->instance;

        // Direct to self so that sqlite unable to create db
        $conn->setOption('database', __DIR__);

        $this->expectException(\RuntimeException::class);
        $conn->connect();
    }
}
