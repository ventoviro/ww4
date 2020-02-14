<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

use PHPUnit\Framework\TestCase;
use Windwalker\Database\Platform\MySQLPlatform;
use Windwalker\Database\Schema\MySQLSchemaManager;
use Windwalker\Database\Test\AbstractDatabaseTestCase;

/**
 * The MySQLSchemaTest class.
 */
class MySQLPlatformTest extends AbstractPlatformTest
{
    protected static $platform = 'MySQL';

    protected static $driver = 'pdo_mysql';
}
