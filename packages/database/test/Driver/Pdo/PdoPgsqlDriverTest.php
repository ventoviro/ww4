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
    protected static $platform = 'PostgreSQL';

    protected static $driverName = 'pdo_pgsql';
}
