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
class MySQLGrammar extends Grammar
{
    /**
     * @var string
     */
    protected static $name = 'MySQL';

    /**
     * @var array
     */
    protected $nameQuote = ['`', '`'];

    protected static $nullDate = '1000-01-01 00:00:00';

    /**
     * If no connection set, we escape it with default function.
     *
     * Since mysql_real_escape_string() has been deprecated, we use an alternative one.
     * Please see:
     * http://stackoverflow.com/questions/4892882/mysql-real-escape-string-for-multibyte-without-a-connection
     *
     * @param string $text
     *
     * @return  string
     */
    protected function unsafeEscape(string $text): string
    {
        return str_replace(
            ['\\', "\0", "\n", "\r", "'", '"', "\x1a"],
            ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'],
            $text
        );
    }
}
