<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query;

use Windwalker\Utilities\Str;

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
     * stripQuoteIfExists
     *
     * @param  string  $value
     * @param  string  $sign
     *
     * @return  string
     */
    public static function stripQuoteIfExists(string $value, string $sign = "'"): string
    {
        if (Str::startsWith($value, $sign) && Str::endsWith($value, $sign)) {
            return static::stripQuote($value);
        }

        return $value;
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

        return $conn ?: [$this->query->getGrammar(), 'localEscape'];
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
