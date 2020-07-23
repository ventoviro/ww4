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
 * The PostgreSQLPlatform class.
 */
class PostgreSQLPlatform extends AbstractPlatformTest
{
    protected static string $platform = 'PostgreSQL';

    protected static string $driver = 'pdo_pgsql';
}
