<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Database\Driver\Pdo\MysqlConnection;
use Windwalker\Database\Driver\Pdo\OdbcConnection;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The MysqlConnectionTest class.
 */
class MysqlConnectionTest extends AbstractDatabaseTestCase
{
    protected static $platform = 'mysql';

    protected static function setupDatabase(): void
    {
    }

    public function testConnect()
    {
        $params = static::getTestParams();

        $conn = new MysqlConnection(
            $params
        );
        $conn->connect();

        show($conn);
    }
}
