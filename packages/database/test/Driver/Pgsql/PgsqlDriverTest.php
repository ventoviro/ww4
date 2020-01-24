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
    protected static $platform = 'pgsql';

    protected static $driverName = 'pgsql';


    /**
     * @see  AbstractDriver::quote
     */
    public function testQuote(): void
    {
        self::assertEquals(
            "'foo''s #hello --options'",
            static::$driver->quote("foo's #hello --options")
        );
    }

    /**
     * @see  AbstractDriver::escape
     */
    public function testEscape(): void
    {
        self::assertEquals(
            "foo''s #hello --options",
            static::$driver->escape("foo's #hello --options")
        );
    }
}
