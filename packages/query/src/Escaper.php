<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Query\Bounded\ParamType;
use Windwalker\Utilities\TypeCast;

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

    /**
     * replaceQueryParams
     *
     * @param  \PDO|callable|Query|mixed  $db
     * @param  string                     $sql
     * @param  array                      $bounded
     *
     * @return  string
     */
    public static function replaceQueryParams($db, $sql, array $bounded): string
    {
        if ($bounded === []) {
            return $sql;
        }

        $params = [];
        $values = [];

        foreach ($bounded as $k => $param) {
            switch ($param['dataType']) {
                case ParamType::STRING:
                    $v = static::quote($db, (string) $param['value']);
                    break;
                default:
                    $v = $param['value'];
                    break;
            }

            if (TypeCast::tryInteger($k, true) !== null) {
                $values[] = $v;
            } else {
                $params[$k] = $v;
            }
        }

        $sql = str_replace('%', '%%', $sql);
        $sql = str_replace('?', '%s', $sql);

        $sql = sprintf($sql, ...$values);

        return preg_replace_callback('/(:[a-zA-Z0-9_]+)/', function ($matched) use ($params, $db) {
            $name = $matched[0];

            $param = $params[$name] ?? $params[ltrim($name, ':')] ?? null;

            if (!$param) {
                return $name;
            }

            return $param;
        }, $sql);
    }
}
