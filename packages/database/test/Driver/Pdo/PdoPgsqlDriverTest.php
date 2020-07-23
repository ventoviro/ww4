<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pdo;

use Windwalker\Database\Test\Driver\AbstractDriverTest;

/**
 * The PdoPgsqlDriverTest class.
 */
class PdoPgsqlDriverTest extends AbstractDriverTest
{
    protected static string $platform = 'PostgreSQL';

    protected static string $driverName = 'pdo_pgsql';
}
