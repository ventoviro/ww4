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
     * @var \PDO|callable|mixed
     */
    protected $connection;

    /**
     * @var Query
     */
    protected $query;

    /**
     * Escaper constructor.
     *
     * @param  \PDO|callable|mixed  $connection
     * @param  Query                $query
     */
    public function __construct($connection, Query $query = null)
    {
        $this->connection = $connection;
        $this->query      = $query;
    }

    public function escape(string $value): string
    {
        return static::tryEscape($this->getConnection(), $value);
    }

    public function quote(string $value): string
    {
        return static::tryQuote($this->getConnection(), $value);
    }

    /**
     * escape
     *
     * @param  \PDO|callable|mixed  $escaper
     * @param  string               $value
     *
     * @return  string
     */
    public static function tryEscape($escaper, string $value): string
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
    public static function tryQuote($escaper, string $value): string
    {
        // PDO has quote method, directly use it.
        if ($escaper instanceof \PDO) {
            return (string) $escaper->quote($value);
        }

        // TODO: Add Database support if available.

        return "'" . static::tryEscape($escaper, $value) . "'";
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
                    $v = static::tryQuote($db, (string) $param['value']);
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

        if ($values !== []) {
            $sql = sprintf($sql, ...$values);
        }

        return preg_replace_callback('/(:[\w_]+)/', function ($matched) use ($params, $db) {
            $name = $matched[0];

            $param = $params[$name] ?? $params[ltrim($name, ':')] ?? null;

            if (!$param) {
                return $name;
            }

            return $param;
        }, $sql);
    }

    /**
     * Method to get property Connection
     *
     * @return  mixed
     */
    public function getConnection()
    {
        if ($this->connection instanceof \WeakReference) {
            $conn = $this->connection->get();
        } else {
            $conn = $this->connection;
        }

        return $conn ?: [$this->query->getGrammar(), 'unsafeEscape'];
    }

    /**
     * Method to set property connection
     *
     * @param  mixed  $connection
     *
     * @return  static  Return self to support chaining.
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
