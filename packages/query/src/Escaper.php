<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

/**
 * The Escaper class.
 */
class Escaper
{
    /**
     * escape
     *
     * @param  \PDO|callable|mixed  $escaper
     * @param  string               $value
     *
     * @return  string
     */
    public static function escape($escaper, string $value): string
    {
        if (is_callable($escaper)) {
            return $escaper($value, [static::class, 'stripQuote']);
        }

        if ($escaper instanceof \PDO) {
            return static::stripQuote((string) $escaper->quote($value));
        }

        // TODO: Add Database support if available.

        return $escaper->escape($value);
    }

    /**
     * quote
     *
     * @param  \PDO|callable|mixed  $escaper
     * @param  string               $value
     *
     * @return  string
     */
    public static function quote($escaper, string $value): string
    {
        // PDO has quote method, directly use it.
        if ($escaper instanceof \PDO) {
            return (string) $escaper->quote($value);
        }

        // TODO: Add Database support if available.

        return "'" . static::escape($escaper, $value) . "'";
    }

    /**
     * stripQuote
     *
     * @param  string  $value
     *
     * @return  string
     */
    public static function stripQuote(string $value): string
    {
        return substr(
            substr(
                (string) $value,
                0,
                -1
            ),
            1
        );
    }
}
