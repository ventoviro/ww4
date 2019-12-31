<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2019 LYRASOFT.
 * @license    GNU General Public License version 2 or later;
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use Windwalker\Test\Helper\TestStringHelper;
use Windwalker\Test\Traits\BaseAssertionTrait;

/**
 * The AbstractQueryTestCase class.
 *
 * @since  2.1
 */
trait QueryTestTrait
{
    use BaseAssertionTrait;

    /**
     * Property quote.
     *
     * @var  array
     */
    protected static $nameQuote = ['"', '"'];

    /**
     * quote
     *
     * @param string $text
     *
     * @return  string
     */
    protected static function qn(string $text): string
    {
        return TestStringHelper::quote($text, static::$nameQuote);
    }

    /**
     * format
     *
     * @param   string $sql
     *
     * @return  String
     */
    protected static function format(string $sql): string
    {
        return \SqlFormatter::format((string) $sql, false);
    }

    public static function assertSqlFormatEquals($sql1, $sql2): void
    {
        self::assertEquals(
            self::format($sql1),
            self::format($sql2)
        );
    }

    public static function assertSqlEquals($sql1, $sql2): void
    {
        self::assertEquals(
            \SqlFormatter::compress($sql1),
            \SqlFormatter::compress($sql2)
        );
    }
}
