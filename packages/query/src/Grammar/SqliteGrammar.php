<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

/**
 * The MySQLGrammar class.
 */
class SqliteGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'Sqlite';

    /**
     * @var array
     */
    protected static $nameQuote = ['`', '`'];
}
