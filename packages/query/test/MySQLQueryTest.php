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
use Windwalker\Query\Grammar\MySQLGrammar;

/**
 * The MySQLQueryTest class.
 */
class MySQLQueryTest extends QueryTest
{
    protected static array $nameQuote = ['`', '`'];

    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function createGrammar(): AbstractGrammar
    {
        return new MySQLGrammar();
    }
}
