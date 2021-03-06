<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Database\Test\Driver\Pgsql;

use Windwalker\Database\Test\Driver\AbstractDriverTest;

/**
 * The PgsqlDriverTest class.
 */
class PgsqlDriverTest extends AbstractDriverTest
{
    protected static string $platform = 'PostgreSQL';

    protected static string $driverName = 'pgsql';

    protected static function setupDatabase(): void
    {
        parent::setupDatabase();
    }
}
