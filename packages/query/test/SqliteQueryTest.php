<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Test;

use Windwalker\Query\Grammar\Grammar;
use Windwalker\Query\Grammar\SqliteGrammar;

/**
 * The SqliteQueryTest class.
 */
class SqliteQueryTest extends QueryTest
{
    protected static $nameQuote = ['`', '`'];

    public static function createGrammar(): Grammar
    {
        return new SqliteGrammar();
    }
}
