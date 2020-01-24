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
 * The PdoDriverTest class.
 */
class PdoDriverTest extends AbstractDriverTest
{
    protected static $platform = 'mysql';

    protected static $driverName = 'pdo_mysql';
}
