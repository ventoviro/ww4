<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Platform;

/**
 * The SQLitePlatformTest class.
 */
class SQLitePlatformTest extends AbstractPlatformTest
{
    protected static $platform = 'SQLite';

    protected static $driver = 'pdo_sqlite';
}
