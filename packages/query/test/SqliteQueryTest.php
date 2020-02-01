<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use Windwalker\Query\Grammar\AbstractGrammar;
use Windwalker\Query\Grammar\SQLiteGrammar;

/**
 * The SqliteQueryTest class.
 */
class SqliteQueryTest extends QueryTest
{
    protected static $nameQuote = ['`', '`'];

    public static function createGrammar(): AbstractGrammar
    {
        return new SQLiteGrammar();
    }
}
