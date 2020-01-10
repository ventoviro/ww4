<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Query;

/**
 * The AbstractGrammar class.
 */
class Grammar
{
    /**
     * @var string
     */
    protected static $name = '';

    /**
     * @var array
     */
    protected static $nameQuote = ['"', '"'];

    /**
     * @var string
     */
    protected static $nullDate = '0000-00-00 00:00:00';

    /**
     * @var string
     */
    protected static $dateFormat = 'Y-m-d H:i:s';

    /**
     * Method to get property Name
     *
     * @return  string
     */
    public static function getName(): string
    {
        return static::$name;
    }

    /**
     * Compile Query object to SQL string.
     *
     * @param  string  $type
     * @param  Query   $query
     *
     * @return  string
     */
    public function compile(string $type, Query $query): string
    {
        $method = 'compile' . ucfirst($type);

        if (!method_exists($this, $method)) {
            throw new \BadMethodCallException(
                sprintf(
                    '%s dose not support "%s" compiled',
                    static::class,
                    $type
                )
            );
        }

        return $this->$method($query);
    }

    public function compileSelect(Query $query): string
    {
        $sql[] = (string) $query->getSelect();

        if ($form = $query->getFrom()) {
            $sql[] = $form;
        }

        if ($where = $query->getWhere()) {
            $sql[] = $where;
        }

        if ($having = $query->getHaving()) {
            $sql[] = $having;
        }

        if ($order = $query->getOrder()) {
            $sql[] = $order;
        }

        return implode(' ', $sql);
    }

    public function compileInsert(Query $query)
    {
        //
    }

    public function compileUpdate(Query $query)
    {
        //
    }

    public function compileDelete(Query $query)
    {
        //
    }

    public function compileUnion(Query $query)
    {
        //
    }

    public function compileCustom(Query $query)
    {
        //
    }

    public function quoteName(string $name): string
    {
        if (stripos($name, ' as ') !== false) {
            [$name, $alias] = preg_split('/ as /i', $name);

            return $this->quoteName($name) . ' AS ' . $this->quoteName($alias);
        }

        if (strpos($name, '.') !== false) {
            [$name1, $name2] = explode('.', $name);

            return $this->quoteName($name1) . '.' . $this->quoteName($name2);
        }

        return static::$nameQuote[0] . $name . static::$nameQuote[1];
    }

    /**
     * If no connection set, we escape it with default function.
     *
     * @param string $text
     *
     * @return  string
     */
    protected function unsafeEscape(string $text): string
    {
        $text = str_replace("'", "''", $text);

        return addcslashes($text, "\000\n\r\\\032");
    }

    public function nullDate(): string
    {
        return static::$nullDate;
    }

    public function dateFormat(): string
    {
        return static::$dateFormat;
    }
}
