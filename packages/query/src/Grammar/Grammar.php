<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2019 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace Windwalker\Query\Grammar;

use Windwalker\Query\Clause\Clause;
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

        if ($join = $query->getJoin()) {
            $sql[] = $join;
        }

        if ($where = $query->getWhere()) {
            $sql[] = $where;
        }

        if ($having = $query->getHaving()) {
            $sql[] = $having;
        }

        if ($group = $query->getGroup()) {
            $sql[] = $group;
        }

        if ($order = $query->getOrder()) {
            $sql[] = $order;
        }

        $sql = $this->compileLimit($query, $sql);

        if ($union = $query->getUnion()) {
            if (!$query->getSelect()) {
                $query->getUnion()->setName('()');
            }

            $sql[] = (string) $union;
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
     * compileLimit
     *
     * @param  Query  $query
     * @param  array  $sql
     *
     * @return  array
     */
    public function compileLimit(Query $query, array $sql): array
    {
        $limit  = $query->getLimit();
        $offset = $query->getOffset();

        if ($limit !== null) {
            $limitOffset = new Clause('LIMIT', (int) $limit, ', ');

            if ($offset !== null) {
                $limitOffset->prepend($offset);
            }

            $sql[] = $limitOffset;
        }

        return $sql;
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
