<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Mysqli;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Driver\Mysqli\MysqliDriver;
use Windwalker\Database\Test\Driver\AbstractDriverTest;

/**
 * The MysqliDriverTest class.
 */
class MysqliDriverTest extends AbstractDriverTest
{
    protected static $platform = 'mysql';

    protected static $driverName = 'mysqli';
}
